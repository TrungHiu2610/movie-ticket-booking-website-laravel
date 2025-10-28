@extends('layouts.admin')

@section('title', 'Quản lý loại ghế')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Quản lý loại ghế</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Loại ghế</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.seat-types.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Thêm loại ghế
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Search and Filter Component -->
<x-admin.search-filter
    searchPlaceholder="Tìm theo tên loại ghế..."
    :filters="[]"
    :sortOptions="[
        'name' => 'Tên loại ghế',
        'surcharge' => 'Phụ phí',
        'created_at' => 'Ngày tạo'
    ]" />

<div class="card shadow-sm">
    <div class="card-body">
        @if($seatTypes->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <p class="text-muted mt-3">Chưa có loại ghế nào</p>
            <a href="{{ route('admin.seat-types.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Thêm loại ghế đầu tiên
            </a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 30%">Tên loại ghế</th>
                        <th style="width: 20%">Phụ thu (đ)</th>
                        <th style="width: 20%">Số ghế</th>
                        <th style="width: 25%" class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($seatTypes as $seatType)
                    <tr>
                        <td>{{ $seatType->id }}</td>
                        <td>
                            <strong>{{ $seatType->name }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-{{ $seatType->surcharge > 0 ? 'warning' : 'secondary' }} text-dark">
                                {{ number_format($seatType->surcharge, 0, ',', '.') }}đ
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-info text-dark">
                                {{ $seatType->seats_count }} ghế
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.seat-types.edit', $seatType) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Sửa
                                </a>
                                <form action="{{ route('admin.seat-types.destroy', $seatType) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa loại ghế này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Hiển thị {{ $seatTypes->firstItem() }} - {{ $seatTypes->lastItem() }}
                trong tổng số {{ $seatTypes->total() }} loại ghế
            </div>
            <div>
                {{ $seatTypes->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection