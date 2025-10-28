@extends('layouts.admin')

@section('title', 'Quản lý Lịch chiếu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý Lịch chiếu</h2>
    <a href="{{ route('admin.showtimes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Thêm suất chiếu mới
    </a>
</div>

@include('components.alert')

<!-- Search and Filter Component -->
<x-admin.search-filter
    searchPlaceholder="Tìm theo tên phim..."
    :filters="[
        [
            'name' => 'movie_id',
            'label' => 'Phim',
            'col' => 3,
            'options' => $movies->toArray()
        ],
        [
            'name' => 'cinema_id',
            'label' => 'Cụm rạp',
            'col' => 3,
            'options' => $cinemas->toArray()
        ],
        [
            'name' => 'date_from',
            'label' => 'Từ ngày',
            'col' => 2,
            'options' => []
        ],
        [
            'name' => 'date_to',
            'label' => 'Đến ngày',
            'col' => 2,
            'options' => []
        ]
    ]"
    :sortOptions="[
        'start_time' => 'Thời gian chiếu',
        'base_price' => 'Giá vé',
        'created_at' => 'Ngày tạo'
    ]" />

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 20%">Phim</th>
                        <th style="width: 20%">Phòng chiếu</th>
                        <th style="width: 15%">Thời gian bắt đầu</th>
                        <th style="width: 15%">Thời gian kết thúc</th>
                        <th style="width: 10%" class="text-end">Giá vé</th>
                        <th style="width: 15%" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($showtimes as $showtime)
                    <tr>
                        <td>{{ $showtimes->firstItem() + $loop->index }}</td>
                        <td>
                            <strong>{{ $showtime->movie->title }}</strong><br>
                            <small class="text-muted">{{ $showtime->movie->duration_minutes }} phút</small>
                        </td>
                        <td>
                            {{ $showtime->theater->name }}<br>
                            <small class="text-muted">{{ $showtime->theater->cinema->name }}</small>
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($showtime->start_time)->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($showtime->end_time)->format('d/m/Y H:i') }}
                        </td>
                        <td class="text-end">
                            <span class="text-primary fw-bold">{{ number_format($showtime->base_price) }}đ</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.showtimes.edit', $showtime) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.showtimes.destroy', $showtime) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Bạn có chắc muốn xóa suất chiếu này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Chưa có suất chiếu nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($showtimes->hasPages())
        <div class="mt-3">
            {{ $showtimes->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection