@extends('layouts.user')

@section('title', 'Trang chủ - Đặt vé xem phim online')

@push('styles')
    <style>
        /* Chatbot Styles */
        .chatbot-toggle {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .chatbot-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .chatbot-toggle i {
            font-size: 28px;
            color: white;
        }

        .chatbot-popup {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 400px;
            max-width: calc(100vw - 60px);
            height: 600px;
            max-height: calc(100vh - 140px);
            background: var(--card-bg);
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            display: none;
            flex-direction: column;
            z-index: 999;
            overflow: hidden;
        }

        .chatbot-popup.show {
            display: flex;
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chatbot-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chatbot-header h6 {
            margin: 0;
            color: white;
            font-weight: 600;
        }

        .chatbot-close {
            background: transparent;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background 0.2s;
        }

        .chatbot-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .chatbot-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f8f9fa;
        }

        .chatbot-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chatbot-messages::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .chatbot-messages::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .chat-message {
            margin-bottom: 15px;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message-bubble {
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 15px;
            word-wrap: break-word;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .user-message .message-bubble {
            background: #667eea;
            color: white;
            margin-left: auto;
        }

        .bot-message .message-bubble {
            background: white;
            color: #333;
        }

        .message-time {
            font-size: 0.75rem;
            color: #999;
            margin-top: 5px;
        }

        .chatbot-input-area {
            padding: 15px;
            background: white;
            border-top: 1px solid #e0e0e0;
        }

        .chatbot-input-group {
            display: flex;
            gap: 10px;
        }

        .chatbot-input {
            flex: 1;
            border: 1px solid #e0e0e0;
            border-radius: 25px;
            padding: 10px 15px;
            outline: none;
            font-size: 14px;
        }

        .chatbot-input:focus {
            border-color: #667eea;
        }

        .chatbot-send-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }

        .chatbot-send-btn:hover:not(:disabled) {
            transform: scale(1.1);
        }

        .chatbot-send-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .chatbot-welcome {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .chatbot-welcome i {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 15px;
        }

        .quick-questions {
            margin-top: 20px;
        }

        .quick-question-btn {
            display: block;
            width: 100%;
            padding: 8px 12px;
            margin: 5px 0;
            background: white;
            border: 1px solid #667eea;
            color: #667eea;
            border-radius: 20px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .quick-question-btn:hover {
            background: #667eea;
            color: white;
        }

        @media (max-width: 768px) {
            .chatbot-popup {
                bottom: 90px;
                right: 10px;
                width: calc(100vw - 20px);
                height: calc(100vh - 120px);
            }

            .chatbot-toggle {
                bottom: 20px;
                right: 20px;
            }
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            height: 80vh;
            min-height: 600px;
            overflow: hidden;
            margin-bottom: 3rem;
        }

        .hero-slider {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .hero-slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .hero-slide.active {
            opacity: 1;
        }

        .hero-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(0, 0, 0, 0.9) 30%, transparent 70%),
                linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent 50%);
            z-index: 1;
        }

        .hero-content {
            position: absolute;
            top: 50%;
            left: 10%;
            transform: translateY(-50%);
            z-index: 2;
            max-width: 600px;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.2;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.8);
        }

        .hero-description {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .hero-buttons .btn {
            margin-right: 1rem;
            margin-bottom: 1rem;
            padding: 0.8rem 2rem;
            font-weight: 600;
            border-radius: 30px;
            transition: all 0.3s ease;
        }

        .btn-watch {
            background: var(--primary-color);
            border: none;
        }

        .btn-watch:hover {
            background: var(--secondary-color);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(229, 9, 20, 0.4);
        }

        .btn-info-outline {
            background: transparent;
            border: 2px solid white;
            color: white;
        }

        .btn-info-outline:hover {
            background: white;
            color: var(--dark-bg);
        }

        /* Section Headers */
        .section-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .section-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .section-header p {
            color: var(--text-secondary);
            margin: 0;
        }

        /* Movie Cards */
        .movie-card {
            background: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            height: 100%;
            border: 1px solid transparent;
        }

        .movie-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary-color);
            box-shadow: 0 15px 40px rgba(229, 9, 20, 0.3);
        }

        .movie-poster {
            position: relative;
            padding-top: 150%;
            overflow: hidden;
        }

        .movie-poster img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .movie-card:hover .movie-poster img {
            transform: scale(1.1);
        }

        .movie-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent 50%);
            display: flex;
            align-items: flex-end;
            padding: 1rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .movie-card:hover .movie-overlay {
            opacity: 1;
        }

        .play-button {
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .movie-card:hover .play-button {
            opacity: 1;
        }

        .movie-info {
            padding: 1.5rem;
        }

        .movie-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .movie-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .movie-genres {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .genre-badge {
            background: rgba(229, 9, 20, 0.2);
            color: var(--primary-color);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            border: 1px solid var(--primary-color);
        }

        .rating {
            background: var(--primary-color);
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
            font-weight: 600;
        }

        /* Coming Soon Section */
        .coming-soon-section {
            background: linear-gradient(135deg, var(--card-bg), var(--dark-bg));
            padding: 4rem 0;
            margin-top: 4rem;
        }

        /* Stats Section */
        .stats-section {
            background: var(--card-bg);
            padding: 3rem 0;
            margin: 4rem 0;
            border-radius: 15px;
        }

        .stat-item {
            text-align: center;
            padding: 2rem;
        }

        .stat-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }

            .hero-description {
                font-size: 1rem;
            }

            .hero-content {
                left: 5%;
                max-width: 90%;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Hero Slider -->
    <section class="hero-section">
        <div class="hero-slider">
            @foreach ($featuredMovies as $index => $movie)
                <div class="hero-slide {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}">
                    <img src="{{ $movie->poster_url ? $movie->poster_url : 'https://via.placeholder.com/1920x1080' }}"
                        alt="{{ $movie->title }}">
                </div>
            @endforeach
        </div>

        <div class="hero-overlay"></div>

        @if ($featuredMovies->isNotEmpty())
            <div class="hero-content" id="heroContent">
                <h1 class="hero-title">{{ $featuredMovies[0]->title }}</h1>
                <div class="hero-description">
                    {{ Str::limit($featuredMovies[0]->description, 200) }}
                </div>
                <div class="movie-meta mb-3">
                    <span class="rating"><i class="bi bi-star-fill"></i> {{ $featuredMovies[0]->age_rating }}</span>
                    <span><i class="bi bi-clock"></i> {{ $featuredMovies[0]->duration_minutes }} phút</span>
                    <span><i class="bi bi-calendar"></i> {{ $featuredMovies[0]->release_date->format('Y') }}</span>
                </div>
                <div class="hero-buttons">
                    <a href="{{ route('movies.show', $featuredMovies[0]) }}" class="btn btn-watch">
                        <i class="bi bi-play-fill"></i> Đặt vé ngay
                    </a>
                    <a href="{{ route('movies.show', $featuredMovies[0]) }}" class="btn btn-info-outline">
                        <i class="bi bi-info-circle"></i> Chi tiết
                    </a>
                </div>
            </div>
        @endif
    </section>

    <!-- Now Showing Section -->
    <section class="container mt-5">
        <div class="section-header">
            <h2><i class="bi bi-film"></i> Phim đang chiếu</h2>
            <p>Những bộ phim hot nhất đang được chiếu tại UniCine</p>
        </div>

        <div class="row g-4">
            @forelse($nowShowingMovies as $movie)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="movie-card" onclick="window.location.href='{{ route('movies.show', $movie) }}'">
                        <div class="movie-poster">
                            <img src="{{ $movie->poster_url ? $movie->poster_url : 'https://via.placeholder.com/300x450' }}"
                                alt="{{ $movie->title }}">
                            <div class="movie-overlay">
                                <div class="play-button">
                                    <i class="bi bi-play-fill"></i>
                                </div>
                            </div>
                        </div>
                        <div class="movie-info">
                            <h5 class="movie-title">{{ $movie->title }}</h5>
                            <div class="movie-meta">
                                <span class="rating">{{ $movie->age_rating }}</span>
                                <span><i class="bi bi-clock"></i> {{ $movie->duration_minutes }}p</span>
                            </div>
                            <div class="movie-meta">
                                <span><i class="bi bi-calendar3"></i> {{ $movie->release_date->format('d/m/Y') }}</span>
                            </div>
                            <div class="movie-genres">
                                @foreach ($movie->genres->take(2) as $genre)
                                    <span class="genre-badge">{{ $genre->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-film" style="font-size: 4rem; color: var(--text-secondary);"></i>
                    <p class="text-secondary mt-3">Hiện chưa có phim đang chiếu</p>
                </div>
            @endforelse
        </div>

        @if ($nowShowingMovies->count() > 0)
            <div class="text-center mt-4">
                <a href="{{ route('movies.index') }}?filter=now_showing" class="btn btn-primary-custom">
                    Xem tất cả phim đang chiếu <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        @endif
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="bi bi-film"></i></div>
                        <div class="stat-number">{{ $totalMovies }}</div>
                        <div class="stat-label">Bộ phim</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="bi bi-building"></i></div>
                        <div class="stat-number">{{ $totalCinemas }}</div>
                        <div class="stat-label">Cụm rạp</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="bi bi-tv"></i></div>
                        <div class="stat-number">{{ $totalTheaters }}</div>
                        <div class="stat-label">Phòng chiếu</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="bi bi-people"></i></div>
                        <div class="stat-number">{{ $totalUsers }}</div>
                        <div class="stat-label">Thành viên</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Coming Soon Section -->
    <section class="coming-soon-section">
        <div class="container">
            <div class="section-header">
                <h2><i class="bi bi-calendar-event"></i> Phim sắp chiếu</h2>
                <p>Đặt vé trước cho những bộ phim bom tấn sắp ra mắt</p>
            </div>

            <div class="row g-4">
                @forelse($comingSoonMovies as $movie)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="movie-card" onclick="window.location.href='{{ route('movies.show', $movie) }}'">
                            <div class="movie-poster">
                                <img src="{{ $movie->poster_url ? $movie->poster_url : 'https://via.placeholder.com/300x450' }}"
                                    alt="{{ $movie->title }}">
                                <div class="movie-overlay">
                                    <div class="play-button">
                                        <i class="bi bi-eye-fill"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="movie-info">
                                <h5 class="movie-title">{{ $movie->title }}</h5>
                                <div class="movie-meta">
                                    <span class="rating">{{ $movie->age_rating }}</span>
                                    <span><i class="bi bi-calendar"></i> {{ $movie->release_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="movie-genres">
                                    @foreach ($movie->genres->take(2) as $genre)
                                        <span class="genre-badge">{{ $genre->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-calendar-event" style="font-size: 4rem; color: var(--text-secondary);"></i>
                        <p class="text-secondary mt-3">Hiện chưa có phim sắp chiếu</p>
                    </div>
                @endforelse
            </div>

            @if ($comingSoonMovies->count() > 0)
                <div class="text-center mt-4">
                    <a href="{{ route('movies.index') }}?filter=coming_soon" class="btn btn-primary-custom">
                        Xem tất cả phim sắp chiếu <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- Chatbot Popup -->
    <div class="chatbot-toggle" id="chatbotToggle">
        <i class="bi bi-robot"></i>
    </div>

    <div class="chatbot-popup" id="chatbotPopup">
        <div class="chatbot-header">
            <h6><i class="bi bi-robot"></i> AI Trợ lý UniCine</h6>
            <button class="chatbot-close" id="chatbotClose">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="chatbot-messages" id="chatbotMessages">
            <div class="chatbot-welcome">
                <i class="bi bi-chat-dots"></i>
                <p>Xin chào! Tôi có thể giúp gì cho bạn?</p>
                <div class="quick-questions">
                    <button class="quick-question-btn"
                        onclick="askQuickQuestion('Tôi thích phim hành động, đang chiếu gì?')">
                        Phim hanh dong dang chieu
                    </button>
                    <button class="quick-question-btn" onclick="askQuickQuestion('Phim nào phù hợp cho trẻ em?')">
                        👶 Phim cho trẻ em
                    </button>
                    <button class="quick-question-btn" onclick="askQuickQuestion('Phim gì hay để hẹn hò?')">
                        ❤️ Phim để hẹn hò
                    </button>
                </div>
            </div>
        </div>

        <div class="chatbot-input-area">
            <form id="chatbotForm" class="chatbot-input-group">
                @csrf
                <input type="text" class="chatbot-input" id="chatbotInput" placeholder="Hỏi về phim..." required>
                <button type="submit" class="chatbot-send-btn" id="chatbotSendBtn">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Chatbot functionality
        const chatbotToggle = document.getElementById('chatbotToggle');
        const chatbotPopup = document.getElementById('chatbotPopup');
        const chatbotClose = document.getElementById('chatbotClose');
        const chatbotForm = document.getElementById('chatbotForm');
        const chatbotInput = document.getElementById('chatbotInput');
        const chatbotSendBtn = document.getElementById('chatbotSendBtn');
        const chatbotMessages = document.getElementById('chatbotMessages');
        let isFirstMessage = true;

        // Toggle chatbot
        chatbotToggle.addEventListener('click', () => {
            chatbotPopup.classList.toggle('show');
            if (chatbotPopup.classList.contains('show')) {
                chatbotInput.focus();
            }
        });

        chatbotClose.addEventListener('click', () => {
            chatbotPopup.classList.remove('show');
        });

        // Send message
        chatbotForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = chatbotInput.value.trim();
            if (!message) return;

            await sendChatMessage(message);
            chatbotInput.value = '';
        });

        async function sendChatMessage(message) {
            // Clear welcome message on first message
            if (isFirstMessage) {
                chatbotMessages.innerHTML = '';
                isFirstMessage = false;
            }

            // Disable input
            chatbotInput.disabled = true;
            chatbotSendBtn.disabled = true;
            chatbotSendBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            // Add user message
            addChatMessage(message, 'user');

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
                    addChatMessage(data.response, 'bot');
                } else {
                    addChatMessage('Xin lỗi, có lỗi xảy ra. Vui lòng thử lại.', 'bot');
                }
            } catch (error) {
                console.error('Error:', error);
                addChatMessage('Xin lỗi, không thể kết nối. Vui lòng thử lại.', 'bot');
            } finally {
                // Re-enable input
                chatbotInput.disabled = false;
                chatbotSendBtn.disabled = false;
                chatbotSendBtn.innerHTML = '<i class="bi bi-send-fill"></i>';
                chatbotInput.focus();
            }
        }

        function addChatMessage(text, role) {
            const now = new Date();
            const time = now.getHours().toString().padStart(2, '0') + ':' +
                now.getMinutes().toString().padStart(2, '0');

            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${role}-message`;
            messageDiv.innerHTML = `
                <div class="message-bubble">
                    ${escapeHtml(text)}
                </div>
                <div class="message-time ${role === 'user' ? 'text-end' : ''}">${time}</div>
            `;

            chatbotMessages.appendChild(messageDiv);
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function askQuickQuestion(question) {
            chatbotInput.value = question;
            chatbotForm.dispatchEvent(new Event('submit'));
        }

        // Hero slider functionality
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hero-slide');
        const heroMovies = @json($featuredMovies);

        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            slides[index].classList.add('active');

            // Update hero content
            if (heroMovies[index]) {
                const movie = heroMovies[index];
                const heroContent = document.getElementById('heroContent');
                heroContent.querySelector('.hero-title').textContent = movie.title;
                heroContent.querySelector('.hero-description').textContent = movie.description.substring(0, 200) + '...';
                heroContent.querySelectorAll('a').forEach(link => {
                    link.href = `/movies/${movie.id}`;
                });
            }
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }

        // Auto slide every 5 seconds
        if (slides.length > 1) {
            setInterval(nextSlide, 5000);
        }
    </script>
@endpush
