@extends('layouts.admin')

@section('title', 'Quản lý Phụ thu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý Phụ thu</h2>
    <a href="{{ route('admin.surcharges.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Thêm phụ thu mới
    </a>
</div>

@include('components.alert')

<!-- Search and Filter Component -->
<x-admin.search-filter
    searchPlaceholder="Tìm theo tên, mô tả phụ thu..."
    :filters="[
        [
            'name' => 'type',
            'label' => 'Loại phụ thu',
            'col' => 3,
            'options' => [
                'DAY_OF_WEEK' => 'Theo ngày trong tuần',
                'SPECIFIC_DATE' => 'Ngày cụ thể',
                'SCREEN_TYPE' => 'Loại màn hình'
            ]
        ]
    ]"
    :sortOptions="[
        'name' => 'Tên phụ thu',
        'amount' => 'Số tiền',
        'type' => 'Loại',
        'created_at' => 'Ngày tạo'
    ]" />

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 25%">Tên phụ thu</th>
                        <th style="width: 15%" class="text-end">Số tiền</th>
                        <th style="width: 10%">Loại</th>
                        <th style="width: 30%">Điều kiện áp dụng</th>
                        <th style="width: 15%" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($surcharges as $surcharge)
                    <tr>
                        <td>{{ $surcharges->firstItem() + $loop->index }}</td>
                        <td>
                            <strong>{{ $surcharge->name }}</strong>
                        </td>
                        <td class="text-end">
                            @if($surcharge->type == 'percentage')
                            <span class="text-success">{{ $surcharge->amount }}%</span>
                            @else
                            <span class="text-primary">{{ number_format($surcharge->amount) }}đ</span>
                            @endif
                        </td>
                        <td>
                            @if($surcharge->type == 'percentage')
                            <span class="badge bg-success">Phần trăm</span>
                            @else
                            <span class="badge bg-primary">Cố định</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $surcharge->apply_condition }}</small>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.surcharges.edit', $surcharge) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.surcharges.destroy', $surcharge) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Bạn có chắc muốn xóa phụ thu này?')">
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
                            Chưa có phụ thu nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($surcharges->hasPages())
        <div class="mt-3">
            {{ $surcharges->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection