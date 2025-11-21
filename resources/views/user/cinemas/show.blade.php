@extends('layouts.user')

@section('title', $cinema->name)

@section('content')
<div class="cinema-detail-container">
    <div class="container py-5">
        <!-- Cinema Header -->
        <div class="cinema-detail-header">
            <div class="cinema-icon-large">
                <i class="bi bi-building"></i>
            </div>
            <div class="cinema-main-info">
                <h1>{{ $cinema->name }}</h1>
                <div class="cinema-address">
                    <i class="bi bi-geo-alt-fill"></i>
                    {{ $cinema->address }}, {{ $cinema->city }}
                </div>
            </div>
        </div>

        <!-- Cinema Info Cards -->
        <div class="info-cards-grid">
            @if($cinema->phone)
            <div class="info-card">
                <div class="info-card-icon">
                    <i class="bi bi-telephone-fill"></i>
                </div>
                <div>
                    <div class="info-card-label">Hotline</div>
                    <div class="info-card-value">{{ $cinema->phone }}</div>
                </div>
            </div>
            @endif

            @if($cinema->email)
            <div class="info-card">
                <div class="info-card-icon">
                    <i class="bi bi-envelope-fill"></i>
                </div>
                <div>
                    <div class="info-card-label">Email</div>
                    <div class="info-card-value">{{ $cinema->email }}</div>
                </div>
            </div>
            @endif

            <div class="info-card">
                <div class="info-card-icon">
                    <i class="bi bi-door-open-fill"></i>
                </div>
                <div>
                    <div class="info-card-label">Số phòng chiếu</div>
                    <div class="info-card-value">{{ $cinema->theaters->count() }} phòng</div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="info-card-label">Tổng số ghế</div>
                    <div class="info-card-value">{{ $cinema->theaters->sum('total_seats') }} ghế</div>
                </div>
            </div>
        </div>

        <!-- Theaters Section -->
        <div class="section-header">
            <h2><i class="bi bi-door-open me-3"></i>Phòng chiếu</h2>
        </div>

        @if($cinema->theaters->isEmpty())
        <div class="empty-section">
            <i class="bi bi-door-closed"></i>
            <p>Chưa có phòng chiếu nào</p>
        </div>
        @else
        <div class="theaters-grid">
            @foreach($cinema->theaters as $theater)
            <div class="theater-card">
                <div class="theater-header">
                    <div class="theater-icon">
                        <i class="bi bi-door-open"></i>
                    </div>
                    <h4>{{ $theater->name }}</h4>
                </div>

                <div class="theater-body">
                    <div class="theater-stat">
                        <i class="bi bi-people"></i>
                        <span>{{ $theater->total_seats }} ghế</span>
                    </div>

                    @php
                    $todayShowtimes = $theater->showtimes()
                    ->whereDate('start_time', today())
                    ->where('start_time', '>=', now())
                    ->count();
                    @endphp

                    <div class="theater-stat">
                        <i class="bi bi-film"></i>
                        <span>{{ $todayShowtimes }} suất chiếu hôm nay</span>
                    </div>
                </div>

                <div class="theater-footer">
                    <span class="theater-status active">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        Đang hoạt động
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Movies Showing Section -->
        @php
        $moviesShowing = \App\Models\Movie::whereHas('showtimes', function($q) use ($cinema) {
        $q->whereHas('theater', function($tq) use ($cinema) {
        $tq->where('cinema_id', $cinema->id);
        })
        ->where('start_time', '>=', now());
        })->with('genres')->take(8)->get();
        @endphp

        @if($moviesShowing->isNotEmpty())
        <div class="section-header">
            <h2><i class="bi bi-film me-3"></i>Phim đang chiếu tại rạp</h2>
        </div>

        <div class="movies-grid">
            @foreach($moviesShowing as $movie)
            <div class="movie-card">
                <div class="movie-poster">
                    <img src="{{ $movie->poster_url ?? 'https://via.placeholder.com/300x450' }}"
                        alt="{{ $movie->title }}">
                    <div class="movie-overlay">
                        <a href="{{ route('movies.show', $movie->id) }}" class="btn-play">
                            <i class="bi bi-play-circle-fill"></i>
                        </a>
                    </div>
                </div>
                <div class="movie-info">
                    <h5 class="movie-title">{{ $movie->title }}</h5>
                    <div class="movie-meta">
                        @if($movie->rating)
                        <span class="rating">
                            <i class="bi bi-star-fill"></i>
                            {{ number_format($movie->rating, 1) }}
                        </span>
                        @endif
                        @if($movie->duration)
                        <span class="duration">
                            <i class="bi bi-clock"></i>
                            {{ $movie->duration }} phút
                        </span>
                        @endif
                    </div>
                    <a href="{{ route('movies.show', $movie->id) }}" class="btn-book-small">
                        <i class="bi bi-ticket-perforated me-2"></i>Đặt vé
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Back Button -->
        <div class="text-center mt-5">
            <a href="{{ route('cinemas.index') }}" class="btn-back-large">
                <i class="bi bi-arrow-left me-2"></i>
                Quay lại danh sách rạp
            </a>
        </div>
    </div>
</div>

<style>
    .cinema-detail-container {
        min-height: calc(100vh - 200px);
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        padding-top: 100px;
    }

    .cinema-detail-header {
        display: flex;
        gap: 30px;
        align-items: center;
        background: var(--card-bg);
        padding: 40px;
        border-radius: 20px;
        margin-bottom: 40px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    }

    .cinema-icon-large {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, var(--primary-color), #d60a5e);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 10px 30px rgba(229, 9, 20, 0.4);
    }

    .cinema-icon-large i {
        font-size: 50px;
        color: #fff;
    }

    .cinema-main-info h1 {
        color: #fff;
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .cinema-address {
        color: rgba(255, 255, 255, 0.8);
        font-size: 18px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .cinema-address i {
        color: var(--primary-color);
        font-size: 20px;
        margin-top: 2px;
    }

    .info-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 50px;
    }

    .info-card {
        background: var(--card-bg);
        padding: 25px;
        border-radius: 15px;
        display: flex;
        gap: 15px;
        align-items: flex-start;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }

    .info-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
    }

    .info-card-icon {
        width: 50px;
        height: 50px;
        background: rgba(229, 9, 20, 0.2);
        border: 2px solid var(--primary-color);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .info-card-icon i {
        font-size: 24px;
        color: var(--primary-color);
    }

    .info-card-label {
        color: rgba(255, 255, 255, 0.6);
        font-size: 13px;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-card-value {
        color: #fff;
        font-size: 16px;
        font-weight: 600;
    }

    .section-header {
        margin-bottom: 30px;
    }

    .section-header h2 {
        color: #fff;
        font-size: 32px;
        font-weight: 700;
        padding-left: 10px;
        border-left: 5px solid var(--primary-color);
        display: flex;
        align-items: center;
    }

    .section-header h2 i {
        color: var(--primary-color);
    }

    .empty-section {
        text-align: center;
        padding: 60px;
        background: var(--card-bg);
        border-radius: 15px;
        margin-bottom: 40px;
    }

    .empty-section i {
        font-size: 80px;
        color: rgba(255, 255, 255, 0.2);
        margin-bottom: 20px;
    }

    .empty-section p {
        color: rgba(255, 255, 255, 0.6);
        font-size: 16px;
    }

    .theaters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 50px;
    }

    .theater-card {
        background: var(--card-bg);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }

    .theater-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .theater-header {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: rgba(255, 255, 255, 0.05);
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    }

    .theater-icon {
        width: 50px;
        height: 50px;
        background: rgba(229, 9, 20, 0.2);
        border: 2px solid var(--primary-color);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .theater-icon i {
        font-size: 24px;
        color: var(--primary-color);
    }

    .theater-header h4 {
        color: #fff;
        font-size: 20px;
        font-weight: 600;
        margin: 0;
    }

    .theater-body {
        padding: 20px;
    }

    .theater-stat {
        display: flex;
        align-items: center;
        gap: 12px;
        color: rgba(255, 255, 255, 0.8);
        font-size: 15px;
        margin-bottom: 12px;
    }

    .theater-stat:last-child {
        margin-bottom: 0;
    }

    .theater-stat i {
        color: var(--primary-color);
        font-size: 18px;
        width: 20px;
    }

    .theater-footer {
        padding: 15px 20px;
        background: rgba(255, 255, 255, 0.03);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .theater-status {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 13px;
        font-weight: 600;
    }

    .theater-status.active {
        background: rgba(76, 175, 80, 0.2);
        color: #4caf50;
        border: 1px solid #4caf50;
    }

    .movies-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .movie-card {
        background: var(--card-bg);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .movie-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .movie-poster {
        position: relative;
        padding-top: 150%;
        overflow: hidden;
        background: #000;
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
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .movie-card:hover .movie-overlay {
        opacity: 1;
    }

    .btn-play {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 40px;
        transition: all 0.3s ease;
    }

    .btn-play:hover {
        color: var(--primary-color);
        transform: scale(1.2);
    }

    .movie-info {
        padding: 15px;
    }

    .movie-title {
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        min-height: 45px;
    }

    .movie-meta {
        display: flex;
        gap: 12px;
        margin-bottom: 12px;
        flex-wrap: wrap;
        font-size: 13px;
    }

    .movie-meta span {
        display: flex;
        align-items: center;
        color: rgba(255, 255, 255, 0.7);
    }

    .movie-meta i {
        margin-right: 4px;
        font-size: 12px;
    }

    .rating i {
        color: #ffd700;
    }

    .btn-book-small {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 10px;
        background: linear-gradient(135deg, var(--primary-color), #d60a5e);
        color: #fff;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-book-small:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(229, 9, 20, 0.4);
        color: #fff;
    }

    .btn-back-large {
        display: inline-flex;
        align-items: center;
        padding: 14px 32px;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .btn-back-large:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        color: #fff;
    }

    @media (max-width: 768px) {
        .cinema-detail-header {
            flex-direction: column;
            text-align: center;
            padding: 30px 20px;
        }

        .cinema-main-info h1 {
            font-size: 28px;
        }

        .cinema-address {
            justify-content: center;
        }

        .info-cards-grid {
            grid-template-columns: 1fr;
        }

        .section-header h2 {
            font-size: 26px;
        }

        .theaters-grid {
            grid-template-columns: 1fr;
        }

        .movies-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }
    }
</style>
@endsection