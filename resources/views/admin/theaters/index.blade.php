@extends('layouts.admin')

@section('title', 'Quản lý Phòng chiếu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý Phòng chiếu</h2>
    <a href="{{ route('admin.theaters.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Thêm phòng chiếu mới
    </a>
</div>

@include('components.alert')

<!-- Search and Filter Component -->
<x-admin.search-filter
    searchPlaceholder="Tìm theo tên phòng chiếu..."
    :filters="[
        [
            'name' => 'cinema_id',
            'label' => 'Cụm rạp',
            'col' => 3,
            'options' => $cinemas->toArray()
        ],
        [
            'name' => 'screen_type',
            'label' => 'Loại màn hình',
            'col' => 2,
            'options' => [
                'standard' => 'Standard',
                '3D' => '3D',
                'IMAX' => 'IMAX'
            ]
        ]
    ]"
    :sortOptions="[
        'name' => 'Tên phòng',
        'total_seats' => 'Số ghế',
        'screen_type' => 'Loại màn hình',
        'created_at' => 'Ngày tạo'
    ]" />

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 20%">Tên phòng</th>
                        <th style="width: 25%">Cụm rạp</th>
                        <th style="width: 20%">Loại màn hình</th>
                        <th style="width: 15%" class="text-center">Số ghế</th>
                        <th style="width: 15%" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($theaters as $theater)
                    <tr>
                        <td>{{ $theaters->firstItem() + $loop->index }}</td>
                        <td>
                            <strong>{{ $theater->name }}</strong>
                        </td>
                        <td>
                            {{ $theater->cinema->name }}<br>
                            <small class="text-muted">{{ $theater->cinema->city }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $theater->screen_type }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $theater->seats->count() }} ghế</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.theaters.edit', $theater) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.theaters.destroy', $theater) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Bạn có chắc muốn xóa phòng chiếu này? Tất cả ghế sẽ bị xóa!')">
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
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Chưa có phòng chiếu nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($theaters->hasPages())
        <div class="mt-3">
            {{ $theaters->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection