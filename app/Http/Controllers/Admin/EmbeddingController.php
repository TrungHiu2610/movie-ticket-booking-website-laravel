<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AIChatbotService;
use App\Models\MovieEmbedding;
use Illuminate\Http\Request;

class EmbeddingController extends Controller
{
    protected $chatbot;

    public function __construct(AIChatbotService $chatbot)
    {
        $this->chatbot = $chatbot;
    }

    public function index()
    {
        $embeddings = MovieEmbedding::with('movie')->paginate(20);

        return view('admin.embeddings.index', compact('embeddings'));
    }

    public function embedAll()
    {
        $this->chatbot->initialize();

        $count = $this->chatbot->embedAllMovies();

        return redirect()->back()->with('success', "Đã embed {$count} phim thành công!");
    }

    public function embedMovie($movieId)
    {
        $success = $this->chatbot->embedMovie($movieId);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Movie embedded successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to embed movie'
        ], 500);
    }
}


