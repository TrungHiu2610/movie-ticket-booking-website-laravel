@extends('layouts.app')

@section('title', 'AI Chatbot - Trợ lý tìm phim')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-gradient text-white py-3"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-robot"></i> AI Chatbot - Trợ lý tìm phim
                            </h5>
                            <button class="btn btn-sm btn-light" onclick="clearChat()">
                                <i class="bi bi-trash"></i> Xóa lịch sử
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0" style="height: 600px; overflow-y: auto; background: #f8f9fa;"
                        id="chatMessages">
                        @if ($history->isEmpty())
                            <div class="text-center py-5">
                                <i class="bi bi-chat-dots" style="font-size: 4rem; color: #ccc;"></i>
                                <p class="text-muted mt-3">Chào bạn! Tôi có thể giúp gì cho bạn?</p>
                                <div class="mt-4">
                                    <p class="text-muted small mb-2">Gợi ý câu hỏi:</p>
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="askQuestion('Tôi thích phim hành động, đang chiếu gì?')">
                                            Phim hành động đang chiếu
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="askQuestion('Phim nào phù hợp cho trẻ em?')">
                                            Phim cho trẻ em
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="askQuestion('Phim gì hay để hẹn hò?')">
                                            Phim để hẹn hò
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="p-3">
                                @foreach ($history as $msg)
                                    <div class="message {{ $msg->role === 'user' ? 'user-message' : 'bot-message' }} mb-3">
                                        <div
                                            class="d-flex {{ $msg->role === 'user' ? 'justify-content-end' : 'justify-content-start' }}">
                                            <div class="message-bubble {{ $msg->role === 'user' ? 'bg-primary text-white' : 'bg-white border' }}"
                                                style="max-width: 75%; padding: 12px 16px; border-radius: 18px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                <div style="white-space: pre-wrap;">{{ $msg->message }}</div>
                                            </div>
                                        </div>
                                        <small
                                            class="text-muted d-block {{ $msg->role === 'user' ? 'text-end' : 'text-start' }} mt-1 px-2">
                                            {{ $msg->created_at->format('H:i') }}
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-white border-top-0 p-3">
                        <form id="chatForm">
                            @csrf
                            <div class="input-group">
                                <input type="text" class="form-control border-0 shadow-sm" id="messageInput"
                                    placeholder="Hỏi tôi về phim... (VD: Tìm phim hành động đang chiếu)"
                                    style="border-radius: 25px 0 0 25px;" required>
                                <button class="btn btn-primary shadow-sm" type="submit" id="sendBtn"
                                    style="border-radius: 0 25px 25px 0; padding: 0 25px;">
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </div>
                        </form>
                        <div class="mt-2 px-2">
                            <small class="text-muted">
                                <i class="bi bi-lightbulb"></i>
                                Hỏi về phim đang chiếu, thể loại, suất chiếu, rạp, độ tuổi...
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .message-bubble {
            word-wrap: break-word;
            animation: slideIn 0.3s ease-out;
        }

        .user-message {
            animation: slideInRight 0.3s ease-out;
        }

        .bot-message {
            animation: slideInLeft 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(20px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-20px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .bg-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        #chatMessages::-webkit-scrollbar {
            width: 6px;
        }

        #chatMessages::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        #chatMessages::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        #chatMessages::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>

    @push('scripts')
        <script>
            const chatForm = document.getElementById('chatForm');
            const messageInput = document.getElementById('messageInput');
            const sendBtn = document.getElementById('sendBtn');
            const chatMessages = document.getElementById('chatMessages');

            chatForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const message = messageInput.value.trim();
                if (!message) return;

                await sendMessage(message);
                messageInput.value = '';
            });

            async function sendMessage(message) {
                // Disable input
                messageInput.disabled = true;
                sendBtn.disabled = true;
                sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                // Add user message
                addMessage(message, 'user');

                try {
                    const response = await fetch('{{ route('chatbot.chat') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            message
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        addMessage(data.response, 'assistant');
                    } else {
                        addMessage('Xin lỗi, có lỗi xảy ra. Vui lòng thử lại.', 'assistant');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    addMessage('Xin lỗi, không thể kết nối. Vui lòng thử lại.', 'assistant');
                } finally {
                    // Re-enable input
                    messageInput.disabled = false;
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = '<i class="bi bi-send-fill"></i>';
                    messageInput.focus();
                }
            }

            function addMessage(text, role) {
                const now = new Date();
                const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${role === 'user' ? 'user-message' : 'bot-message'} mb-3`;
                messageDiv.innerHTML = `
        <div class="d-flex ${role === 'user' ? 'justify-content-end' : 'justify-content-start'}">
            <div class="message-bubble ${role === 'user' ? 'bg-primary text-white' : 'bg-white border'}" style="max-width: 75%; padding: 12px 16px; border-radius: 18px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="white-space: pre-wrap;">${escapeHtml(text)}</div>
            </div>
        </div>
        <small class="text-muted d-block ${role === 'user' ? 'text-end' : 'text-start'} mt-1 px-2">
            ${time}
        </small>
    `;

                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function askQuestion(question) {
                messageInput.value = question;
                chatForm.dispatchEvent(new Event('submit'));
            }

            async function clearChat() {
                if (!confirm('Xóa tất cả lịch sử chat?')) return;

                try {
                    const response = await fetch('{{ route('chatbot.clear') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    if (response.ok) {
                        location.reload();
                    }
                } catch (error) {
                    alert('Có lỗi xảy ra!');
                }
            }

            // Auto scroll to bottom on load
            window.addEventListener('load', () => {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
        </script>
    @endpush
@endsection
