@extends('layouts.admin')

@section('title', 'Danh sách Phim')
@section('page-title', 'Quản lý Phim')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Danh sách phim</h5>
    <a href="{{ route('admin.movies.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm phim mới
    </a>
</div>

<!-- Search and Filter Component -->
<x-admin.search-filter
    searchPlaceholder="Tìm theo tên phim, mô tả..."
    :filters="[
        [
            'name' => 'status',
            'label' => 'Trạng thái',
            'col' => 2,
            'options' => [
                'active' => 'Đang chiếu',
                'upcoming' => 'Sắp chiếu',
                'ended' => 'Đã kết thúc'
            ]
        ],
        [
            'name' => 'age_rating',
            'label' => 'Độ tuổi',
            'col' => 2,
            'options' => [
                'P' => 'P - Phổ biến',
                'C13' => 'C13 - Từ 13 tuổi',
                'C16' => 'C16 - Từ 16 tuổi',
                'C18' => 'C18 - Từ 18 tuổi'
            ]
        ]
    ]"
    :sortOptions="[
        'title' => 'Tên phim',
        'base_price' => 'Giá vé',
        'release_date' => 'Ngày phát hành',
        'duration_minutes' => 'Thời lượng',
        'created_at' => 'Ngày tạo'
    ]" />

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px">Poster</th>
                        <th>Tiêu đề</th>
                        <th>Thời lượng</th>
                        <th>Giá vé cơ bản</th>
                        <th>Ngày phát hành</th>
                        <th>Độ tuổi</th>
                        <th>Trạng thái</th>
                        <th style="width: 150px">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movies as $movie)
                    <tr>
                        <td>
                            @if($movie->poster_url)
                            <img src="{{ Storage::url($movie->poster_url) }}"
                                alt="{{ $movie->title }}"
                                class="img-thumbnail"
                                style="width: 60px; height: 80px; object-fit: cover;">
                            @else
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
                                style="width: 60px; height: 80px;">
                                <i class="bi bi-film"></i>
                            </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $movie->title }}</strong>
                            <br>
                            <small class="text-muted">
                                @foreach($movie->genres as $genre)
                                <span class="badge bg-secondary">{{ $genre->name }}</span>
                                @endforeach
                            </small>
                        </td>
                        <td>{{ $movie->duration_minutes }} phút</td>
                        <td>{{ number_format($movie->base_price, 0, ',', '.') }} VND</td>
                        <td>{{ \Carbon\Carbon::parse($movie->release_date)->format('d/m/Y') }}</td>
                        <td><span class="badge bg-info">{{ $movie->age_rating }}</span></td>
                        <td>
                            @if($movie->status == 'now_showing')
                            <span class="badge bg-success">Đang chiếu</span>
                            @else
                            <span class="badge bg-warning">Sắp chiếu</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.movies.edit', $movie) }}"
                                    class="btn btn-outline-primary"
                                    title="Sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.movies.destroy', $movie) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa phim này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Chưa có phim nào.
                            <a href="{{ route('admin.movies.create') }}">Thêm phim mới</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movies->hasPages())
        <div class="mt-4">
            {{ $movies->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection