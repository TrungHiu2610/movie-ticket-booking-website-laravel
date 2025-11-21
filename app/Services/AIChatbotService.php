<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\MovieEmbedding;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AIChatbotService
{
    protected $gemini;
    protected $vectorDb;

    public function __construct(GeminiService $gemini, VectorDBService $vectorDb)
    {
        $this->gemini = $gemini;
        $this->vectorDb = $vectorDb;
    }


    public function initialize()
    {
        return $this->vectorDb->getOrCreateCollection();
    }

    public function embedMovie($movieId)
    {
        $movie = Movie::with(['genres', 'directors', 'actors', 'showtimes.theater.cinema'])
            ->find($movieId);

        if (!$movie) {
            Log::warning("Movie {$movieId} not found for embedding");
            return false;
        }

        $content = $this->buildMovieContent($movie);
        $embedding = $this->gemini->generateEmbedding($content);

        if (!$embedding) {
            Log::error("Failed to generate embedding for movie {$movieId}");
            return false;
        }

        $meta = [
            'movie_id' => $movieId,
            'title' => $movie->title,
            'genres' => $movie->genres->pluck('name')->implode(', '),
        ];

        $result = $this->vectorDb->addEmbedding($movieId, $embedding, $content, $meta);

        if ($result) {
            MovieEmbedding::updateOrCreate(
                ['movie_id' => $movieId],
                [
                    'content' => $content,
                    'embedded_at' => now()
                ]
            );
            Log::info("Successfully embedded movie {$movieId}");
        }

        return $result;
    }


    public function embedAllMovies()
    {
        $movies = Movie::all();
        $successCount = 0;
        $failCount = 0;

        foreach ($movies as $movie) {
            if ($this->embedMovie($movie->id)) {
                $successCount++;
            } else {
                $failCount++;
            }

            // Rate limiting - Gemini free tier: 60 req/min
            usleep(1000000); // 1 second delay
        }

        Log::info("Embedding completed: {$successCount} success, {$failCount} failed");

        return $successCount;
    }


    public function chat($question, $userId = null, $sessionId = null)
    {
        DB::beginTransaction();
        try {
            // Save user message
            $userMessage = ChatMessage::create([
                'user_id' => $userId,
                'session_id' => $sessionId ?? session()->getId(),
                'role' => 'user',
                'content' => $question
            ]);

            // Phân tích intent của câu hỏi
            $intent = $this->analyzeIntent($question);
            Log::info('Question intent', ['question' => $question, 'intent' => $intent]);

            $context = [];
            $conversationHistory = [];

            // LUÔN lấy conversation history để AI hiểu맥락
            $conversationHistory = $this->getRecentHistory($userId, $sessionId, 6);

            // Handle booking intent specially
            if ($intent === 'booking') {
                Log::info('Booking intent detected, extracting movie from history');
                $movieName = $this->detectBookingIntent($question, $conversationHistory);

                if ($movieName) {
                    // Re-search the movie to get booking_url
                    Log::info('Re-searching movie for booking', ['movie' => $movieName]);
                    $context = $this->searchMovies($movieName);
                } else {
                    // No movie found in history, search based on question
                    $context = $this->searchMovies($question);
                }
            } elseif ($intent === 'greeting' || $intent === 'acknowledgment') {
                Log::info('Skipping search for greeting/acknowledgment');
            } else {
                // Normal query - search movies
                $context = $this->searchMovies($question);

                Log::info('Movie search results', [
                    'question' => $question,
                    'found_movies' => count($context),
                    'movie_titles' => array_column($context, 'title')
                ]);
            }

            // Generate response using Gemini (với context và FULL conversation history)
            $response = $this->gemini->generateResponse($question, $context, $conversationHistory);

            // Save assistant message
            $assistantMessage = ChatMessage::create([
                'user_id' => $userId,
                'session_id' => $sessionId ?? session()->getId(),
                'role' => 'assistant',
                'content' => $response,
                'context' => json_encode($context)
            ]);

            DB::commit();

            return [
                'success' => true,
                'response' => $response,
                'context' => $context
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Chat error: ' . $e->getMessage());

            return [
                'success' => false,
                'response' => 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại.',
                'error' => $e->getMessage()
            ];
        }
    }


    private function analyzeIntent($question)
    {
        $question = mb_strtolower(trim($question));

        // Pure greetings
        if (preg_match('/^(xin chào|chào|hello|hi|hey)$/iu', $question)) {
            return 'greeting';
        }

        // Pure acknowledgments (very short, no content)
        if (preg_match('/^(ừ|uh|uhm|ok|okay)$/iu', $question)) {
            return 'acknowledgment';
        }

        // Booking intent
        if (preg_match('/(đặt vé|mua vé|book|booking|đặt chỗ|đặt ghế)/iu', $question)) {
            return 'booking';
        }

        // Everything else should search
        return 'query';
    }


    private function detectBookingIntent($question, $conversationHistory)
    {
        // Check if user wants to book tickets
        if (!preg_match('/(đặt vé|mua vé|book|booking|đặt chỗ|muốn xem)/iu', $question)) {
            return null;
        }

        // Extract movie name from recent history (last 4 messages)
        $recentHistory = array_slice($conversationHistory, -4);
        $movieNames = [];

        foreach ($recentHistory as $msg) {
            if ($msg['role'] === 'user') {
                // Try to find movie names in user messages
                // Common Vietnamese movie patterns
                if (preg_match('/(?:phim |xem )([A-Za-z0-9 ]+)(?:\s|$|\?|:|có|là|gì)/iu', $msg['content'], $matches)) {
                    $movieNames[] = trim($matches[1]);
                }
            }
        }

        Log::info('Booking intent detected', [
            'question' => $question,
            'extracted_movies' => $movieNames
        ]);

        return !empty($movieNames) ? $movieNames[0] : null;
    }


    private function searchMovies($question)
    {
        try {
            // Generate embedding for question
            $cacheKey = 'chat_embedding_' . md5($question);
            $questionEmbedding = \Cache::remember($cacheKey, 3600, function () use ($question) {
                return $this->gemini->generateEmbedding($question);
            });

            if (!$questionEmbedding) {
                Log::warning('Failed to generate embedding, using fallback only');
                return $this->fallbackSearch($question);
            }

            // Delay to avoid rate limit
            sleep(2);

            // Vector search
            $results = $this->vectorDb->query($questionEmbedding, 5, 0.25); // Giảm threshold xuống 0.25
            $context = $this->buildContextFromResults($results);

            Log::info('Vector search completed', [
                'found' => count($context),
                'titles' => array_column($context, 'title')
            ]);

            // Fallback if vector search failed
            if (empty($context)) {
                Log::info('Vector search empty, trying fallback');
                $context = $this->fallbackSearch($question);
            }

            return $context;
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            return $this->fallbackSearch($question);
        }
    }

    private function buildMovieContent($movie)
    {
        $content = "Tên phim: {$movie->title}\n";
        $content .= "Mô tả: {$movie->description}\n";
        $content .= "Thể loại: " . $movie->genres->pluck('name')->implode(', ') . "\n";
        $content .= "Đạo diễn: " . $movie->directors->pluck('name')->implode(', ') . "\n";
        $content .= "Diễn viên: " . $movie->actors->pluck('name')->implode(', ') . "\n";
        $content .= "Thời lượng: {$movie->duration} phút\n";
        $content .= "Độ tuổi: {$movie->age_rating}\n";

        // Add showtimes info
        if ($movie->showtimes->count() > 0) {
            $content .= "Đang chiếu tại: ";
            $cinemas = $movie->showtimes->map(function ($showtime) {
                return $showtime->theater->cinema->name;
            })->unique()->implode(', ');
            $content .= $cinemas . "\n";

            $showtimes = $movie->showtimes->take(5)->map(function ($showtime) {
                return $showtime->start_time->format('H:i d/m/Y') . ' - ' . $showtime->theater->cinema->name;
            })->implode(', ');
            $content .= "Suất chiếu gần nhất: " . $showtimes . "\n";
        } else {
            $content .= "Chưa có suất chiếu\n";
        }

        return $content;
    }


    private function buildContextFromResults($results)
    {
        if (!$results || empty($results['ids']) || empty($results['ids'][0])) {
            return [];
        }

        $context = [];
        $movieIds = [];

        // Extract movie IDs from results
        if (isset($results['metadatas'][0])) {
            foreach ($results['metadatas'][0] as $metadata) {
                if (isset($metadata['movie_id'])) {
                    $movieIds[] = $metadata['movie_id'];
                }
            }
        }

        if (empty($movieIds)) {
            return [];
        }

        // Fetch full movie data
        $movies = Movie::with(['genres', 'directors', 'actors', 'showtimes.theater.cinema'])
            ->whereIn('id', $movieIds)
            ->get();

        foreach ($movies as $movie) {
            $showtimes = $movie->showtimes->take(3)->map(function ($showtime) {
                return $showtime->start_time->format('H:i d/m') . ' - ' . $showtime->theater->cinema->name;
            })->implode(', ');

            $context[] = [
                'id' => $movie->id,
                'title' => $movie->title,
                'description' => $movie->description,
                'genres' => $movie->genres->pluck('name')->implode(', '),
                'director' => $movie->directors->pluck('name')->implode(', '),
                'actors' => $movie->actors->pluck('name')->implode(', '),
                'duration' => $movie->duration,
                'age_rating' => $movie->age_rating,
                'showtimes' => $showtimes ?: 'Chưa có suất chiếu',
                'poster' => $movie->poster_url,
                'booking_url' => url('/movies/' . $movie->id) // Thêm URL đặt vé
            ];
        }

        return $context;
    }


    private function fallbackSearch($question)
    {
        try {
            // Trích xuất từ khoá từ câu hỏi (loại bỏ stopwords)
            $keywords = $this->extractKeywords($question);

            if (empty($keywords)) {
                // Nếu không trích xuất được keyword, lấy các phim đang chiếu
                $movies = Movie::with(['genres', 'directors', 'actors', 'showtimes.theater.cinema'])
                    ->whereHas('showtimes', function ($query) {
                        $query->where('start_time', '>=', now());
                    })
                    ->limit(5)
                    ->get();

                // Nếu không có phim đang chiếu, lấy bất kỳ phim nào
                if ($movies->isEmpty()) {
                    Log::info('No movies currently showing, getting any movies');
                    $movies = Movie::with(['genres', 'directors', 'actors', 'showtimes.theater.cinema'])
                        ->limit(5)
                        ->get();
                }
            } else {
                // Tìm kiếm full-text trong title, description - tìm từng keyword riêng
                $movies = Movie::with(['genres', 'directors', 'actors', 'showtimes.theater.cinema'])
                    ->where(function ($query) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            // Tìm kiếm từng keyword trong title (case-insensitive)
                            $query->orWhere('title', 'ILIKE', "%{$keyword}%");
                        }
                    })
                    ->orWhere(function ($query) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            $query->orWhere('description', 'ILIKE', "%{$keyword}%");
                        }
                    })
                    ->orWhereHas('genres', function ($query) use ($keywords) {
                        $query->where(function ($q) use ($keywords) {
                            foreach ($keywords as $keyword) {
                                $q->orWhere('name', 'ILIKE', "%{$keyword}%");
                            }
                        });
                    })
                    ->orWhereHas('directors', function ($query) use ($keywords) {
                        $query->where(function ($q) use ($keywords) {
                            foreach ($keywords as $keyword) {
                                $q->orWhere('name', 'ILIKE', "%{$keyword}%");
                            }
                        });
                    })
                    ->orWhereHas('actors', function ($query) use ($keywords) {
                        $query->where(function ($q) use ($keywords) {
                            foreach ($keywords as $keyword) {
                                $q->orWhere('name', 'ILIKE', "%{$keyword}%");
                            }
                        });
                    })
                    ->limit(5) // Tăng từ 3 lên 5 để tìm nhiều hơn
                    ->get();

                // Nếu vẫn không tìm thấy, lấy bất kỳ phim nào
                if ($movies->isEmpty()) {
                    Log::info('No movies found with keywords, getting any movies');
                    $movies = Movie::with(['genres', 'directors', 'actors', 'showtimes.theater.cinema'])
                        ->limit(5)
                        ->get();
                }
            }

            $context = [];
            foreach ($movies as $movie) {
                $showtimes = $movie->showtimes->take(3)->map(function ($showtime) {
                    return $showtime->start_time->format('H:i d/m') . ' - ' . $showtime->theater->cinema->name;
                })->implode(', ');

                $context[] = [
                    'id' => $movie->id,
                    'title' => $movie->title,
                    'description' => $movie->description,
                    'genres' => $movie->genres->pluck('name')->implode(', '),
                    'director' => $movie->directors->pluck('name')->implode(', '),
                    'actors' => $movie->actors->pluck('name')->implode(', '),
                    'duration' => $movie->duration,
                    'age_rating' => $movie->age_rating,
                    'showtimes' => $showtimes ?: 'Chưa có suất chiếu',
                    'poster' => $movie->poster_url,
                    'booking_url' => url('/movies/' . $movie->id) // Thêm URL đặt vé
                ];
            }

            Log::info('Fallback search found ' . count($context) . ' movies for keywords: ' . implode(', ', $keywords));
            return $context;
        } catch (\Exception $e) {
            Log::error('Fallback search error: ' . $e->getMessage());
            return [];
        }
    }

    private function extractKeywords($question)
    {
        // Danh sách stopwords tiếng Việt
        $stopwords = [
            'có',
            'là',
            'và',
            'mà',
            'cho',
            'với',
            'của',
            'này',
            'đó',
            'kia',
            'nên',
            'thì',
            'chỉ',
            'đã',
            'sẽ',
            'được',
            'như',
            'không',
            'không',
            'chưa',
            'bị',
            'gì',
            'ai',
            'cái',
            'nào',
            'đâu',
            'sau',
            'trước',
            'trong',
            'ngoài',
            'trên',
            'dưới',
            'lên',
            'xuống',
            'có',
            'phải',
            'mấy',
            'nên',
            'nhỉ',
            'hay',
            'phim',
            'suất',
            'chiếu',
            'lúc',
            'giờ',
            'rạp',
            'xem',
            'đặt',
            'vé',
            'làm',
            'sao',
            'thế',
            'như',
            'thế'
        ];

        // Chuyển thành chữ thường và tách từ
        $words = preg_split('/\s+/', mb_strtolower($question), -1, PREG_SPLIT_NO_EMPTY);

        // Loại bỏ stopwords và từ ngắn
        $keywords = array_filter($words, function ($word) use ($stopwords) {
            return !in_array($word, $stopwords) && mb_strlen($word) > 2;
        });

        return array_values($keywords);
    }


    private function getRecentHistory($userId = null, $sessionId = null, $limit = 5)
    {
        $query = ChatMessage::query();

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId ?? session()->getId());
        }

        $messages = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();

        // Format for Gemini API
        return $messages->map(function ($msg) {
            return [
                'role' => $msg->role === 'user' ? 'user' : 'assistant',
                'content' => $msg->content
            ];
        })->toArray();
    }


    public function getChatHistory($userId = null, $sessionId = null, $limit = 50)
    {
        $query = ChatMessage::query();

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId ?? session()->getId());
        }

        return $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }


    public function clearChatHistory($userId = null, $sessionId = null)
    {
        $query = ChatMessage::query();

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId ?? session()->getId());
        }

        return $query->delete();
    }
}
