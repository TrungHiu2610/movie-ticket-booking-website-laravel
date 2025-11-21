<?php

namespace App\Listeners;

use App\Events\MovieUpdated;
use App\Services\AIChatbotService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EmbedMovieListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $chatbot;

    /**
     * Create the event listener.
     */
    public function __construct(AIChatbotService $chatbot)
    {
        $this->chatbot = $chatbot;
    }

    /**
     * Handle the event.
     */
    public function handle(MovieUpdated $event): void
    {
        $this->chatbot->embedMovie($event->movie->id);
    }
}
