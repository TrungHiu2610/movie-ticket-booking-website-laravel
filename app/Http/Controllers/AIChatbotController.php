<?php

namespace App\Http\Controllers;

use App\Services\AIChatbotService;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AIChatbotController extends Controller
{
    protected $chatbot;

    public function __construct(AIChatbotService $chatbot)
    {
        $this->chatbot = $chatbot;
    }

    public function index()
    {
        $history = $this->chatbot->getChatHistory(
            Auth::id(),
            session()->getId()
        );

        return view('chatbot.index', compact('history'));
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

        $result = $this->chatbot->chat(
            $request->message,
            Auth::id(),
            session()->getId()
        );

        return response()->json($result);
    }

    public function history()
    {
        $history = $this->chatbot->getChatHistory(
            Auth::id(),
            session()->getId()
        );

        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }

    public function clear()
    {
        ChatMessage::where(function ($query) {
            $query->where('user_id', Auth::id())
                ->orWhere('session_id', session()->getId());
        })->delete();

        return response()->json([
            'success' => true,
            'message' => 'Chat history cleared'
        ]);
    }
}


