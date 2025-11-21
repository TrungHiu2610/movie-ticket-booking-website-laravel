@extends('layouts.user')

@section('title', 'Hệ thống rạp')

@section('content')
<div class="cinemas-container">
    <div class="container py-5">
        <div class="cinemas-header">
            <h1><i class="bi bi-building me-3"></i>Hệ thống rạp UniCine</h1>
            <p>Tìm rạp chiếu gần bạn</p>
        </div>

        @if($cinemas->isEmpty())
        <div class="empty-state">
            <i class="bi bi-building"></i>
            <h3>Chưa có rạp nào</h3>
            <p>Hệ thống rạp đang được cập nhật</p>
        </div>
        @else
        @foreach($cinemas as $city => $citycinemas)
        <div class="city-section">
            <h2 class="city-title">
                <i class="bi bi-geo-alt-fill me-2"></i>
                {{ $city }}
            </h2>

            <div class="cinemas-grid">
                @foreach($citycinemas as $cinema)
                <div class="cinema-card">
                    <div class="cinema-header">
                        <div class="cinema-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="cinema-basic-info">
                            <h4>{{ $cinema->name }}</h4>
                            <div class="cinema-location">
                                <i class="bi bi-geo-alt"></i>
                                {{ $cinema->address }}
                            </div>
                        </div>
                    </div>

                    <div class="cinema-body">
                        <div class="cinema-info-grid">
                            @if($cinema->phone)
                            <div class="info-item">
                                <i class="bi bi-telephone-fill"></i>
                                <div>
                                    <div class="info-label">Hotline</div>
                                    <div class="info-value">{{ $cinema->phone }}</div>
                                </div>
                            </div>
                            @endif

                            @if($cinema->email)
                            <div class="info-item">
                                <i class="bi bi-envelope-fill"></i>
                                <div>
                                    <div class="info-label">Email</div>
                                    <div class="info-value">{{ $cinema->email }}</div>
                                </div>
                            </div>
                            @endif

                            <div class="info-item">
                                <i class="bi bi-door-open-fill"></i>
                                <div>
                                    <div class="info-label">Phòng chiếu</div>
                                    <div class="info-value">{{ $cinema->theaters->count() }} phòng</div>
                                </div>
                            </div>

                            <div class="info-item">
                                <i class="bi bi-film"></i>
                                <div>
                                    <div class="info-label">Phim đang chiếu</div>
                                    <div class="info-value">
                                        {{ $cinema->theaters->flatMap->showtimes->unique('movie_id')->count() }} phim
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($cinema->theaters->count() > 0)
                        <div class="theaters-list">
                            <h6><i class="bi bi-door-open me-2"></i>Phòng chiếu:</h6>
                            <div class="theaters-badges">
                                @foreach($cinema->theaters->take(6) as $theater)
                                <span class="theater-badge">{{ $theater->name }}</span>
                                @endforeach
                                @if($cinema->theaters->count() > 6)
                                <span class="theater-badge more">+{{ $cinema->theaters->count() - 6 }}</span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="cinema-footer">
                        <a href="{{ route('cinemas.show', $cinema->id) }}" class="btn-details">
                            <i class="bi bi-info-circle me-2"></i>
                            Xem chi tiết
                        </a>
                        <a href="{{ route('movies.index') }}" class="btn-book">
                            <i class="bi bi-ticket-perforated me-2"></i>
                            Đặt vé ngay
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
        @endif
    </div>
</div>

<style>
    .cinemas-container {
        min-height: calc(100vh - 200px);
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        padding-top: 100px;
    }

    .cinemas-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .cinemas-header h1 {
        color: #fff;
        font-size: 42px;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .cinemas-header p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 18px;
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: var(--card-bg);
        border-radius: 20px;
        max-width: 600px;
        margin: 0 auto;
    }

    .empty-state i {
        font-size: 100px;
        color: rgba(255, 255, 255, 0.2);
        margin-bottom: 25px;
    }

    .empty-state h3 {
        color: #fff;
        font-size: 28px;
        margin-bottom: 15px;
    }

    .empty-state p {
        color: rgba(255, 255, 255, 0.6);
        font-size: 16px;
    }

    .city-section {
        margin-bottom: 60px;
    }

    .city-title {
        color: #fff;
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 30px;
        padding-left: 10px;
        border-left: 5px solid var(--primary-color);
        display: flex;
        align-items: center;
    }

    .city-title i {
        color: var(--primary-color);
    }

    .cinemas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
    }

    .cinema-card {
        background: var(--card-bg);
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
    }

    .cinema-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    }

    .cinema-header {
        display: flex;
        gap: 20px;
        padding: 25px;
        background: rgba(255, 255, 255, 0.05);
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    }

    .cinema-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-color), #d60a5e);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 5px 15px rgba(229, 9, 20, 0.3);
    }

    .cinema-icon i {
        font-size: 30px;
        color: #fff;
    }

    .cinema-basic-info {
        flex: 1;
    }

    .cinema-basic-info h4 {
        color: #fff;
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .cinema-location {
        color: rgba(255, 255, 255, 0.7);
        font-size: 14px;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .cinema-location i {
        color: var(--primary-color);
        margin-top: 2px;
        flex-shrink: 0;
    }

    .cinema-body {
        padding: 25px;
    }

    .cinema-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }

    .info-item {
        display: flex;
        gap: 12px;
        background: rgba(255, 255, 255, 0.05);
        padding: 15px;
        border-radius: 10px;
    }

    .info-item i {
        font-size: 22px;
        color: var(--primary-color);
        margin-top: 3px;
    }

    .info-label {
        color: rgba(255, 255, 255, 0.6);
        font-size: 12px;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        color: #fff;
        font-size: 14px;
        font-weight: 600;
    }

    .theaters-list {
        padding: 20px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
    }

    .theaters-list h6 {
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 12px;
        font-size: 14px;
        display: flex;
        align-items: center;
    }

    .theaters-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .theater-badge {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: rgba(255, 255, 255, 0.8);
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
    }

    .theater-badge.more {
        background: linear-gradient(135deg, rgba(229, 9, 20, 0.2), rgba(214, 10, 94, 0.2));
        border-color: var(--primary-color);
        color: var(--primary-color);
        font-weight: 600;
    }

    .cinema-footer {
        display: flex;
        gap: 10px;
        padding: 20px 25px;
        background: rgba(255, 255, 255, 0.03);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .cinema-footer a {
        flex: 1;
        padding: 12px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        text-align: center;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-details {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .btn-details:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        color: #fff;
    }

    .btn-book {
        background: linear-gradient(135deg, var(--primary-color), #d60a5e);
        color: #fff;
    }

    .btn-book:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(229, 9, 20, 0.4);
        color: #fff;
    }

    @media (max-width: 992px) {
        .cinemas-grid {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        }

        .cinema-info-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .cinemas-header h1 {
            font-size: 32px;
        }

        .city-title {
            font-size: 26px;
        }

        .cinemas-grid {
            grid-template-columns: 1fr;
        }

        .cinema-footer {
            flex-direction: column;
        }
    }
</style>
@endsection