@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row g-4">
        <!-- Total Movies -->
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Phim</h6>
                            <h2 class="mb-0">{{ \App\Models\Movie::count() }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-film"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-primary bg-opacity-75 border-0">
                    <a href="{{ route('admin.movies.index') }}" class="text-white text-decoration-none small">
                        Xem chi tiết <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Cinemas -->
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Rạp chiếu</h6>
                            <h2 class="mb-0">{{ \App\Models\Cinema::count() }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-success bg-opacity-75 border-0">
                    <a href="{{ route('admin.cinemas.index') }}" class="text-white text-decoration-none small">
                        Xem chi tiết <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Genres -->
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Thể loại</h6>
                            <h2 class="mb-0">{{ \App\Models\Genre::count() }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-tags"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-info bg-opacity-75 border-0">
                    <a href="{{ route('admin.genres.index') }}" class="text-white text-decoration-none small">
                        Xem chi tiết <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Actors -->
        <div class="col-md-3">
            <div class="card bg-warning shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 text-dark">Diễn viên</h6>
                            <h2 class="mb-0 text-dark">{{ \App\Models\Actor::count() }}</h2>
                        </div>
                        <div class="fs-1 text-dark">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-warning bg-opacity-75 border-0">
                    <a href="{{ route('admin.actors.index') }}" class="text-dark text-decoration-none small fw-bold">
                        Xem chi tiết <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <!-- Recent Movies -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history"></i> Phim mới thêm
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tiêu đề</th>
                                    <th>Thời lượng</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày thêm</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\Movie::latest()->take(5)->get() as $movie)
                                    <tr>
                                        <td>{{ $movie->title }}</td>
                                        <td>{{ $movie->duration_minutes }} phút</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $movie->status == 'now_showing' ? 'success' : 'warning' }}">
                                                {{ $movie->status == 'now_showing' ? 'Đang chiếu' : 'Sắp chiếu' }}
                                            </span>
                                        </td>
                                        <td>{{ $movie->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Chưa có phim nào
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning"></i> Thao tác nhanh
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.movies.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Thêm phim mới
                        </a>
                        <a href="{{ route('admin.genres.create') }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle"></i> Thêm thể loại
                        </a>
                        <a href="{{ route('admin.actors.create') }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle"></i> Thêm diễn viên
                        </a>
                        <a href="{{ route('admin.cinemas.create') }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle"></i> Thêm rạp chiếu
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
