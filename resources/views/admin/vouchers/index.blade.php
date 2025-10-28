@extends('layouts.admin')

@section('title', 'Quản lý Voucher')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý Voucher</h2>
    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Thêm voucher mới
    </a>
</div>

@include('components.alert')

<!-- Search and Filter Component -->
<x-admin.search-filter
    searchPlaceholder="Tìm theo mã voucher, mô tả..."
    :filters="[
        [
            'name' => 'status',
            'label' => 'Trạng thái',
            'col' => 2,
            'options' => [
                'active' => 'Đang kích hoạt',
                'inactive' => 'Đã vô hiệu'
            ]
        ],
        [
            'name' => 'expiry',
            'label' => 'Hạn sử dụng',
            'col' => 2,
            'options' => [
                'valid' => 'Còn hiệu lực',
                'expired' => 'Đã hết hạn'
            ]
        ]
    ]"
    :sortOptions="[
        'code' => 'Mã voucher',
        'discount_percentage' => '% Giảm giá',
        'discount_amount' => 'Số tiền giảm',
        'valid_from' => 'Ngày bắt đầu',
        'valid_to' => 'Ngày hết hạn',
        'created_at' => 'Ngày tạo'
    ]" />

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 15%">Mã voucher</th>
                        <th style="width: 15%" class="text-end">Giảm giá</th>
                        <th style="width: 15%" class="text-end">Giảm tối đa</th>
                        <th style="width: 12%">Hết hạn</th>
                        <th style="width: 13%" class="text-center">Sử dụng</th>
                        <th style="width: 10%" class="text-center">Trạng thái</th>
                        <th style="width: 15%" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $voucher)
                    <tr>
                        <td>{{ $vouchers->firstItem() + $loop->index }}</td>
                        <td>
                            <strong class="font-monospace">{{ $voucher->code }}</strong>
                        </td>
                        <td class="text-end">
                            @if($voucher->discount_percentage)
                            <span class="text-success">{{ $voucher->discount_percentage }}%</span>
                            @elseif($voucher->discount_amount)
                            <span class="text-primary">{{ number_format($voucher->discount_amount) }}đ</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($voucher->max_discount_amount)
                            {{ number_format($voucher->max_discount_amount) }}đ
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($voucher->expires_at)->format('d/m/Y') }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">
                                {{ $voucher->usage_count }}
                                @if($voucher->usage_limit)
                                / {{ $voucher->usage_limit }}
                                @endif
                            </span>
                        </td>
                        <td class="text-center">
                            @if(\Carbon\Carbon::parse($voucher->expires_at)->isPast())
                            <span class="badge bg-danger">Hết hạn</span>
                            @elseif($voucher->usage_limit && $voucher->usage_count >= $voucher->usage_limit)
                            <span class="badge bg-secondary">Đã hết</span>
                            @else
                            <span class="badge bg-success">Khả dụng</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Bạn có chắc muốn xóa voucher này?')">
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
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Chưa có voucher nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vouchers->hasPages())
        <div class="mt-3">
            {{ $vouchers->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection