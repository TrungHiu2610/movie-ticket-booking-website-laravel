@extends('layouts.user')

@section('title', $movie->title . ' - Đặt vé xem phim')

@push('styles')
    <style>
        /* Movie Hero Section */
        .movie-hero {
            position: relative;
            height: 70vh;
            min-height: 500px;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .movie-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(0, 0, 0, 0.95) 40%, rgba(0, 0, 0, 0.5) 70%, transparent),
                linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent 60%);
        }

        .movie-hero-content {
            position: relative;
            z-index: 1;
            padding-top: 8rem;
        }

        .movie-poster-large {
            width: 100%;
            max-width: 350px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
            transition: transform 0.3s ease;
        }

        .movie-poster-large:hover {
            transform: scale(1.05);
        }

        .movie-title-large {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.8);
        }

        .movie-meta-large {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .movie-meta-large .item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.1rem;
        }

        .rating-large {
            background: var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .movie-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }

        .genre-tag {
            display: inline-block;
            background: rgba(229, 9, 20, 0.2);
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .genre-tag:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Content Sections */
        .content-section {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Cast Section */
        .cast-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1.5rem;
        }

        .cast-item {
            text-align: center;
        }

        .cast-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--dark-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-size: 2.5rem;
            color: var(--primary-color);
            border: 3px solid var(--primary-color);
        }

        .cast-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .cast-role {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        /* Showtime Section */
        .date-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            overflow-x: auto;
            padding-bottom: 1rem;
        }

        .date-tab {
            min-width: 120px;
            padding: 1rem;
            background: var(--dark-bg);
            border: 2px solid transparent;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .date-tab:hover {
            border-color: var(--primary-color);
            transform: translateY(-3px);
        }

        .date-tab.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .date-tab .day {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .date-tab.active .day {
            color: white;
        }

        .date-tab .date {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .showtime-content {
            display: none;
        }

        .showtime-content.active {
            display: block;
        }

        .cinema-group {
            background: var(--dark-bg);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .cinema-name {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .showtime-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem;
        }

        .showtime-card {
            background: var(--card-bg);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .showtime-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(229, 9, 20, 0.3);
        }

        .showtime-time {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .showtime-theater {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        .showtime-price {
            font-size: 1.1rem;
            color: var(--primary-color);
            font-weight: 600;
        }

        .showtime-seats {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-top: 0.5rem;
        }

        .no-showtimes {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
        }

        /* Trailer Modal */
        .modal-content {
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-body {
            padding: 0;
        }

        .trailer-embed {
            width: 100%;
            aspect-ratio: 16/9;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .movie-title-large {
                font-size: 2rem;
            }

            .movie-poster-large {
                max-width: 250px;
            }

            .date-tabs {
                flex-wrap: nowrap;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Movie Hero -->
    <section class="movie-hero"
        style="background-image: url('{{ $movie->poster_url ? $movie->poster_url : 'https://via.placeholder.com/1920x1080' }}');">
        <div class="movie-hero-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-4 mb-4">
                        <img src="{{ $movie->poster_url ? $movie->poster_url : 'https://via.placeholder.com/300x450' }}"
                            alt="{{ $movie->title }}" class="movie-poster-large">
                    </div>
                    <div class="col-lg-9 col-md-8">
                        <h1 class="movie-title-large">{{ $movie->title }}</h1>

                        <div class="movie-meta-large">
                            <div class="item">
                                <span class="rating-large">
                                    <i class="bi bi-star-fill"></i> {{ $movie->age_rating }}
                                </span>
                            </div>
                            <div class="item">
                                <i class="bi bi-clock"></i> {{ $movie->duration_minutes }} phút
                            </div>
                            <div class="item">
                                <i class="bi bi-calendar"></i> {{ $movie->release_date->format('d/m/Y') }}
                            </div>
                        </div>

                        <div class="mb-3">
                            @foreach ($movie->genres as $genre)
                                <span class="genre-tag">{{ $genre->name }}</span>
                            @endforeach
                        </div>

                        <p class="movie-description">{{ $movie->description }}</p>

                        @if ($movie->trailer_url)
                            <button class="btn btn-primary-custom btn-lg" data-bs-toggle="modal"
                                data-bs-target="#trailerModal">
                                <i class="bi bi-play-circle"></i> Xem Trailer
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container my-5">
        <!-- Ratings & Reviews -->
        <div class="content-section">
            <h2 class="section-title">
                <i class="bi bi-star-fill"></i> Đánh giá
            </h2>

            <div class="row mb-4">
                <div class="col-md-4 text-center border-end border-secondary">
                    <div class="display-3 fw-bold text-warning mb-2">
                        {{ $averageRating > 0 ? number_format($averageRating, 1) : 'N/A' }}
                    </div>
                    <div class="text-warning mb-2" style="font-size: 1.5rem;">
                        @if ($averageRating > 0)
                            @for ($i = 1; $i <= 10; $i++)
                                @if ($i <= floor($averageRating))
                                    ★
                                @elseif($i - 0.5 <= $averageRating)
                                    <span style="position: relative; display: inline-block;">
                                        <span style="color: #6c757d;">★</span>
                                        <span
                                            style="position: absolute; left: 0; overflow: hidden; width: 50%; color: #ffc107;">★</span>
                                    </span>
                                @else
                                    <span style="color: #6c757d;">★</span>
                                @endif
                            @endfor
                        @else
                            <span class="text-secondary">Chưa có đánh giá</span>
                        @endif
                    </div>
                    <p class="text-secondary">{{ $totalRatings }} đánh giá</p>
                </div>

                <div class="col-md-8">
                    <h5 class="mb-3">Phân bố đánh giá</h5>
                    @for ($i = 10; $i >= 1; $i--)
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2" style="min-width: 60px;">{{ $i }} sao</span>
                            <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ $ratingDistribution[$i] }}%"
                                    aria-valuenow="{{ $ratingDistribution[$i] }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <span class="text-secondary" style="min-width: 45px;">{{ $ratingDistribution[$i] }}%</span>
                        </div>
                    @endfor
                </div>
            </div>

            @if ($ratings->count() > 0)
                <h5 class="mt-4 mb-3">Nhận xét từ khán giả</h5>
                @foreach ($ratings as $rating)
                    <div class="bg-dark rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong class="text-primary">{{ $rating->user->name }}</strong>
                                <div class="text-warning">
                                    @for ($i = 1; $i <= 10; $i++)
                                        @if ($i <= $rating->rating)
                                            ★
                                        @else
                                            <span class="text-secondary">★</span>
                                        @endif
                                    @endfor
                                    <span class="text-white ms-2">{{ $rating->rating }}/10</span>
                                </div>
                            </div>
                            <small class="text-secondary">{{ $rating->created_at->diffForHumans() }}</small>
                        </div>
                        @if ($rating->review)
                            <p class="mb-0 text-secondary">{{ $rating->review }}</p>
                        @endif
                    </div>
                @endforeach

                <div class="mt-3">
                    {{ $ratings->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-chat-quote text-secondary" style="font-size: 3rem;"></i>
                    <p class="text-secondary mt-3">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá phim này!</p>
                </div>
            @endif
        </div>

        <!-- Cast & Crew -->
        @if ($movie->actors->count() > 0 || $movie->directors->count() > 0)
            <div class="content-section">
                <h2 class="section-title">
                    <i class="bi bi-people"></i> Diễn viên & Đạo diễn
                </h2>

                <div class="cast-grid">
                    @foreach ($movie->directors as $director)
                        <div class="cast-item">
                            <div class="cast-avatar">
                                <i class="bi bi-camera-reels"></i>
                            </div>
                            <div class="cast-name">{{ $director->name }}</div>
                            <div class="cast-role">Đạo diễn</div>
                        </div>
                    @endforeach

                    @foreach ($movie->actors->take(8) as $actor)
                        <div class="cast-item">
                            <div class="cast-avatar">
                                <i class="bi bi-person"></i>
                            </div>
                            <div class="cast-name">{{ $actor->name }}</div>
                            <div class="cast-role">Diễn viên</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Showtimes -->
        <div class="content-section">
            <h2 class="section-title">
                <i class="bi bi-clock-history"></i> Lịch chiếu
            </h2>

            @if ($showtimes->count() > 0)
                <!-- Date Tabs -->
                <div class="date-tabs">
                    @foreach ($showtimes as $date => $dateShowtimes)
                        <div class="date-tab {{ $loop->first ? 'active' : '' }}" data-date="{{ $date }}">
                            <div class="day">{{ \Carbon\Carbon::parse($date)->locale('vi')->isoFormat('dddd') }}</div>
                            <div class="date">{{ \Carbon\Carbon::parse($date)->format('d/m') }}</div>
                        </div>
                    @endforeach
                </div>

                <!-- Showtime Content -->
                @foreach ($showtimes as $date => $dateShowtimes)
                    <div class="showtime-content {{ $loop->first ? 'active' : '' }}" data-date="{{ $date }}">
                        @php
                            $groupedByCinema = $dateShowtimes->groupBy('theater.cinema.id');
                        @endphp

                        @foreach ($groupedByCinema as $cinemaId => $cinemaShowtimes)
                            <div class="cinema-group">
                                <div class="cinema-name">
                                    <i class="bi bi-building"></i>
                                    {{ $cinemaShowtimes->first()->theater->cinema->name }}
                                    <small
                                        class="text-secondary">({{ $cinemaShowtimes->first()->theater->cinema->city }})</small>
                                </div>

                                <div class="showtime-grid">
                                    @foreach ($cinemaShowtimes as $showtime)
                                        <div class="showtime-card"
                                            onclick="window.location.href='{{ route('booking.seats', $showtime->id) }}'">
                                            <div class="showtime-time">
                                                {{ $showtime->start_time->format('H:i') }}
                                            </div>
                                            <div class="showtime-theater">
                                                {{ $showtime->theater->name }}
                                                @if ($showtime->theater->screen_type != 'standard')
                                                    <span
                                                        class="badge bg-danger">{{ $showtime->theater->screen_type }}</span>
                                                @endif
                                            </div>
                                            <div class="showtime-price">
                                                {{ number_format($showtime->base_price, 0, ',', '.') }}đ
                                            </div>
                                            <div class="showtime-seats">
                                                <i class="bi bi-chair"></i>
                                                {{ $showtime->available_seats ?? $showtime->theater->total_seats }} ghế
                                                trống
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <div class="no-showtimes">
                    <i class="bi bi-calendar-x" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Chưa có lịch chiếu</h4>
                    <p>Phim này hiện chưa có suất chiếu nào. Vui lòng quay lại sau!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Trailer Modal -->
    @if ($movie->trailer_url)
        <div class="modal fade" id="trailerModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $movie->title }} - Trailer</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <iframe class="trailer-embed" src="{{ $movie->trailer_url }}" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        // Date tabs functionality
        document.querySelectorAll('.date-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const date = this.getAttribute('data-date');

                // Update active tab
                document.querySelectorAll('.date-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                // Update active content
                document.querySelectorAll('.showtime-content').forEach(c => c.classList.remove('active'));
                document.querySelector(`.showtime-content[data-date="${date}"]`).classList.add('active');
            });
        });

        // Stop video when modal is closed
        const trailerModal = document.getElementById('trailerModal');
        if (trailerModal) {
            trailerModal.addEventListener('hidden.bs.modal', function() {
                const iframe = this.querySelector('iframe');
                if (iframe) {
                    iframe.src = iframe.src; // Reload iframe to stop video
                }
            });
        }
    </script>
@endpush
