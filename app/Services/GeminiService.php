<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private $apiKey;
    private $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    public function generateEmbedding($text, $maxRetries = 2)
    {
        $attempt = 0;
        $delay = 3;

        while ($attempt < $maxRetries) {
            try {
                $url = "{$this->baseUrl}/models/text-embedding-004:embedContent";

                $response = Http::timeout(30)->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $this->apiKey,
                ])->post($url, [
                    'model' => 'models/text-embedding-004',
                    'content' => [
                        'parts' => [
                            ['text' => $text]
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['embedding']['values'] ?? null;
                }

                if ($response->status() === 429) {
                    $attempt++;
                    if ($attempt < $maxRetries) {
                        $waitTime = $delay * pow(2, $attempt - 1) + rand(0, 2000) / 1000;
                        Log::warning("Gemini embedding rate limit hit. Retry {$attempt}/{$maxRetries} after {$waitTime}s");
                        sleep($waitTime);
                        continue;
                    }
                }

                Log::error('Gemini embedding error: ' . $response->body());
                return null;
            } catch (\Exception $e) {
                Log::error('Gemini embedding exception: ' . $e->getMessage());

                $attempt++;
                if ($attempt < $maxRetries) {
                    $waitTime = $delay * pow(2, $attempt - 1);
                    Log::warning("Gemini embedding network error. Retry {$attempt}/{$maxRetries} after {$waitTime}s");
                    sleep($waitTime);
                    continue;
                }

                return null;
            }
        }

        return null;
    }


    public function generateResponse($question, $context = [], $conversationHistory = [], $maxRetries = 2)
    {
        $attempt = 0;
        $baseDelay = 3; // seconds

        while ($attempt < $maxRetries) {
            try {
                // Build context text
                $contextText = $this->buildContext($context);

                $systemPrompt = "Bạn là AI assistant thông minh của UniCine - chuyên gia phim và đặt vé.\n\n";
                $systemPrompt .= "KHA NANG DAC BIET:\n";
                $systemPrompt .= "Nho TOAN BO lich su hoi thoai (conversation history)\n";
                $systemPrompt .= "Hieu ngu canh va ket noi thong tin giua cac cau\n";
                $systemPrompt .= "Khong hoi lai thong tin da duoc cung cap\n\n";

                if (!empty($context)) {
                    $systemPrompt .= "DATABASE PHIM:\n{$contextText}\n\n";
                } else {
                    $systemPrompt .= "LUU Y: Khong co phim phu hop trong database, nhung hay kiem tra LICH SU HOI THOAI - co the khach da hoi ve phim truoc do!\n\n";
                }

                $systemPrompt .= "QUY TAC TRA LOI (QUAN TRONG):\n\n";

                $systemPrompt .= "1. PHAN TICH INTENT:\n";
                $systemPrompt .= "   • 'minh muon dat ve' / 'dat ve' → Khach muon BOOKING\n";
                $systemPrompt .= "   • 'noi dung' / 've gi' → Gioi thieu phim\n";
                $systemPrompt .= "   • 'luc may gio' / 'suat chieu' → Thong tin lich chieu\n\n";

                $systemPrompt .= "2. KET NOI NGU CANH:\n";
                $systemPrompt .= "   • Neu khach vua hoi ve PHIM X, sau do noi 'toi muon dat ve'\n";
                $systemPrompt .= "     → Hieu ngay la dat ve PHIM X (dung hoi lai!)\n";
                $systemPrompt .= "   • Tim ten phim trong lich su → Tim booking_url → Dua link ngay\n\n";

                $systemPrompt .= "3. DAT VE (QUAN TRONG NHAT):\n";
                $systemPrompt .= "   • DATABASE PHIM o tren LUON co booking_url cho moi phim\n";
                $systemPrompt .= "   • Khi khach muon dat ve → DOC KY DATABASE PHIM → LAY booking_url\n";
                $systemPrompt .= "   • BAT BUOC format response:\n";
                $systemPrompt .= "     'De dat ve phim [TEN PHIM], ban truy cap: [booking_url]\n";
                $systemPrompt .= "     Huong dan: Chon suat chieu → Chon ghe → Thanh toan → Nhan ma QR qua email'\n";
                $systemPrompt .= "   • TUYET DOI KHONG noi 'chua ho tro' neu co booking_url trong DATABASE\n\n";

                $systemPrompt .= "4. KHONG DUOC:\n";
                $systemPrompt .= "   Hoi lai 'Ban muon xem phim gi?' khi khach da noi\n";
                $systemPrompt .= "   Noi 'khong co thong tin' khi thong tin co trong lich su\n";
                $systemPrompt .= "   Quen context - luon doc ky lich su truoc khi tra loi\n\n";

                $systemPrompt .= "STYLE: Than thien, ngan gon (2-4 cau)";

                // Build contents array with conversation history
                $contents = [];

                // Add system prompt
                $contents[] = [
                    'role' => 'user',
                    'parts' => [['text' => $systemPrompt]]
                ];
                $contents[] = [
                    'role' => 'model',
                    'parts' => [['text' => 'Toi hieu! Toi se nho lich su hoi thoai, ket noi ngu canh thong minh, va dua link dat ve ngay khi khach can.']]
                ];

                // Add FULL conversation history (Gemini 2.0 có context window lớn)
                foreach ($conversationHistory as $msg) {
                    $contents[] = [
                        'role' => $msg['role'] === 'user' ? 'user' : 'model',
                        'parts' => [['text' => $msg['content']]]
                    ];
                }

                // Add current question
                $contents[] = [
                    'role' => 'user',
                    'parts' => [['text' => $question]]
                ];

                // Call Gemini API
                $url = "{$this->baseUrl}/models/gemini-2.0-flash:generateContent";

                $response = Http::timeout(30)->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $this->apiKey,
                ])->post($url, [
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 1024,
                    ]
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                    if (!$text) {
                        Log::error('Gemini response missing text: ' . json_encode($data));
                        return 'Xin lỗi, tôi không thể trả lời câu hỏi này lúc này. Vui lòng thử lại.';
                    }

                    return $text;
                }

                // Nếu là lỗi 429 (Too Many Requests), retry với exponential backoff + jitter
                if ($response->status() === 429) {
                    $attempt++;
                    if ($attempt < $maxRetries) {
                        // Exponential backoff: 5s, 10s, 20s + random jitter 0-2s
                        $delay = $baseDelay * pow(2, $attempt - 1) + rand(0, 2000) / 1000;
                        Log::warning("Gemini API rate limit hit. Retry {$attempt}/{$maxRetries} after {$delay}s");
                        sleep($delay);
                        continue;
                    }
                    return 'Xin lỗi, hệ thống đang quá tải. Vui lòng thử lại sau vài giây.';
                }

                Log::error('Gemini response error: ' . $response->status() . ' - ' . $response->body());
                return 'Xin lỗi, tôi không thể trả lời câu hỏi này lúc này. Vui lòng thử lại.';
            } catch (\Exception $e) {
                Log::error('Gemini response exception: ' . $e->getMessage());

                // Retry on network errors
                $attempt++;
                if ($attempt < $maxRetries) {
                    $delay = $baseDelay * pow(2, $attempt - 1);
                    Log::warning("Gemini API network error. Retry {$attempt}/{$maxRetries} after {$delay}s");
                    sleep($delay);
                    continue;
                }

                return 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau.';
            }
        }

        return 'Xin lỗi, hệ thống đang quá tải. Vui lòng thử lại sau.';
    }


    private function buildContext($context)
    {
        if (empty($context)) {
            return 'Không tìm thấy thông tin phim phù hợp trong hệ thống.';
        }

        $text = '';
        foreach ($context as $index => $movie) {
            $text .= "\n--- PHIM " . ($index + 1) . " ---\n";
            $text .= "Tên phim: {$movie['title']}\n";
            $text .= "Mô tả: {$movie['description']}\n";
            $text .= "Thể loại: {$movie['genres']}\n";
            $text .= "Đạo diễn: {$movie['director']}\n";
            $text .= "Diễn viên: {$movie['actors']}\n";
            $text .= "Thời lượng: {$movie['duration']} phút\n";
            $text .= "Giới hạn tuổi: {$movie['age_rating']}\n";
            $text .= "Suất chiếu: {$movie['showtimes']}\n";
            if (isset($movie['booking_url'])) {
                $text .= "Link đặt vé: {$movie['booking_url']}\n";
            }
        }

        return $text;
    }
}
