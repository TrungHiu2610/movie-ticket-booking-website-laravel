@extends('layouts.user')

@section('title', 'Danh sách phim')

@section('content')
    <div class="movies-container">
        <div class="container py-5">
            <div class="movies-header">
                <h1><i class="bi bi-film me-3"></i>Danh sách phim</h1>
                <p>Khám phá những bộ phim đang chiếu và sắp chiếu</p>
            </div>

            <!-- Search & Filter Section -->
            <div class="search-filter-section">
                <form method="GET" action="{{ route('movies.index') }}" class="filters-form">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm phim..."
                                    value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="now_showing" {{ request('status') == 'now_showing' ? 'selected' : '' }}>
                                    Đang chiếu
                                </option>
                                <option value="coming_soon" {{ request('status') == 'coming_soon' ? 'selected' : '' }}>
                                    Sắp chiếu
                                </option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <select name="genre" class="form-select">
                                <option value="">Tất cả thể loại</option>
                                @foreach ($genres ?? [] as $genre)
                                    <option value="{{ $genre->id }}"
                                        {{ request('genre') == $genre->id ? 'selected' : '' }}>
                                        {{ $genre->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <select name="actor" class="form-select">
                                <option value="">Tất cả diễn viên</option>
                                @foreach ($actors ?? [] as $actor)
                                    <option value="{{ $actor->id }}"
                                        {{ request('actor') == $actor->id ? 'selected' : '' }}>
                                        {{ $actor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <select name="director" class="form-select">
                                <option value="">Tất cả đạo diễn</option>
                                @foreach ($directors ?? [] as $director)
                                    <option value="{{ $director->id }}"
                                        {{ request('director') == $director->id ? 'selected' : '' }}>
                                        {{ $director->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="sort_by" class="form-select">
                                <option value="release_date" {{ request('sort_by') == 'release_date' ? 'selected' : '' }}>
                                    Ngày phát hành
                                </option>
                                <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>
                                    Tên phim
                                </option>
                                <option value="rating" {{ request('sort_by') == 'rating' ? 'selected' : '' }}>
                                    Đánh giá
                                </option>
                                <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>
                                    Giá vé
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="sort_order" class="form-select">
                                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>
                                    Giảm dần
                                </option>
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>
                                    Tăng dần
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn-filter w-100">
                                <i class="bi bi-funnel me-2"></i>Lọc
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Movies Grid -->
            @if ($movies->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-film"></i>
                    <h3>Không tìm thấy phim nào</h3>
                    <p>Thử thay đổi bộ lọc hoặc tìm kiếm khác</p>
                    <a href="{{ route('movies.index') }}" class="btn-reset">
                        <i class="bi bi-arrow-clockwise me-2"></i>
                        Đặt lại bộ lọc
                    </a>
                </div>
            @else
                <div class="movies-grid">
                    @foreach ($movies as $movie)
                        <div class="movie-card">
                            <div class="movie-poster">
                                <img src="{{ $movie->poster_url ?? 'https://via.placeholder.com/300x450' }}"
                                    alt="{{ $movie->title }}">
                                <div class="movie-overlay">
                                    <a href="{{ route('movies.show', $movie->id) }}" class="btn-play">
                                        <i class="bi bi-play-circle-fill"></i>
                                    </a>
                                </div>
                                @if ($movie->status == 'coming_soon')
                                    <div class="coming-soon-badge">
                                        <i class="bi bi-clock-fill me-1"></i>
                                        Sắp chiếu
                                    </div>
                                @endif
                            </div>
                            <div class="movie-info">
                                <h5 class="movie-title">{{ $movie->title }}</h5>
                                <div class="movie-meta">
                                    @if ($movie->rating)
                                        <span class="rating">
                                            <i class="bi bi-star-fill"></i>
                                            {{ number_format($movie->rating, 1) }}
                                        </span>
                                    @endif
                                    @if ($movie->duration)
                                        <span class="duration">
                                            <i class="bi bi-clock"></i>
                                            {{ $movie->duration }} phút
                                        </span>
                                    @endif
                                </div>
                                <div class="movie-genres">
                                    @foreach ($movie->genres->take(3) as $genre)
                                        <span class="genre-badge">{{ $genre->name }}</span>
                                    @endforeach
                                </div>
                                @if ($movie->release_date)
                                    <div class="release-date">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        {{ \Carbon\Carbon::parse($movie->release_date)->format('d/m/Y') }}
                                    </div>
                                @endif
                                <a href="{{ route('movies.show', $movie->id) }}" class="btn-book">
                                    @if ($movie->status == 'now_showing')
                                        <i class="bi bi-ticket-perforated me-2"></i>Đặt vé ngay
                                    @else
                                        <i class="bi bi-info-circle me-2"></i>Xem chi tiết
                                    @endif
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $movies->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .movies-container {
            min-height: calc(100vh - 200px);
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            padding-top: 100px;
        }

        .movies-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .movies-header h1 {
            color: #fff;
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .movies-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 18px;
        }

        .search-filter-section {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.5);
            font-size: 18px;
        }

        .search-box .form-control {
            padding-left: 45px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            height: 50px;
            border-radius: 10px;
        }

        .search-box .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(229, 9, 20, 0.25);
        }

        .search-box .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            height: 50px;
            border-radius: 10px;
        }

        .form-select:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(229, 9, 20, 0.25);
        }

        .form-select option {
            background: #1f1f1f;
            color: #fff;
        }

        .btn-filter {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), #d60a5e);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(229, 9, 20, 0.4);
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
            margin-bottom: 30px;
        }

        .btn-reset {
            display: inline-flex;
            align-items: center;
            padding: 14px 32px;
            background: linear-gradient(135deg, var(--primary-color), #d60a5e);
            color: #fff;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(229, 9, 20, 0.4);
            color: #fff;
        }

        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .movie-card {
            background: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }

        .movie-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
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
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 50px;
            transition: all 0.3s ease;
        }

        .btn-play:hover {
            color: var(--primary-color);
            transform: scale(1.2);
        }

        .coming-soon-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #ff9800, #ff5722);
            color: #fff;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            box-shadow: 0 3px 10px rgba(255, 152, 0, 0.4);
        }

        .movie-info {
            padding: 20px;
        }

        .movie-title {
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 12px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            min-height: 50px;
        }

        .movie-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .movie-meta span {
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .movie-meta i {
            margin-right: 5px;
            font-size: 14px;
        }

        .rating i {
            color: #ffd700;
        }

        .movie-genres {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 12px;
        }

        .genre-badge {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .release-date {
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .btn-book {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary-color), #d60a5e);
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(229, 9, 20, 0.4);
            color: #fff;
        }

        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 50px;
        }

        .pagination-wrapper .pagination {
            gap: 8px;
        }

        .pagination-wrapper .page-link {
            background: var(--card-bg);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            border-radius: 8px;
            padding: 10px 16px;
            transition: all 0.3s ease;
        }

        .pagination-wrapper .page-link:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
        }

        .pagination-wrapper .page-item.active .page-link {
            background: linear-gradient(135deg, var(--primary-color), #d60a5e);
            border-color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .movies-header h1 {
                font-size: 32px;
            }

            .movies-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 20px;
            }

            .search-filter-section {
                padding: 20px;
            }

            .btn-filter {
                margin-top: 15px;
            }
        }
    </style>
@endsection
