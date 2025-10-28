@extends('layouts.admin')

@section('title', 'Chỉnh sửa voucher')

@section('content')
<div class="mb-4">
    <h2>Chỉnh sửa voucher</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.vouchers.index') }}">Voucher</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="code" class="form-label">Mã voucher <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control font-monospace @error('code') is-invalid @enderror"
                            id="code"
                            name="code"
                            value="{{ old('code', $voucher->code) }}"
                            style="text-transform: uppercase"
                            autofocus>
                        @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Chọn 1 trong 2 loại giảm giá:</strong> Giảm theo % HOẶC giảm cố định (đồng)
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discount_percentage" class="form-label">Giảm theo % (tùy chọn)</label>
                                <input type="number"
                                    class="form-control @error('discount_percentage') is-invalid @enderror"
                                    id="discount_percentage"
                                    name="discount_percentage"
                                    value="{{ old('discount_percentage', $voucher->discount_percentage) }}"
                                    step="0.01"
                                    min="0"
                                    max="100">
                                @error('discount_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discount_amount" class="form-label">Giảm cố định (đồng) (tùy chọn)</label>
                                <input type="number"
                                    class="form-control @error('discount_amount') is-invalid @enderror"
                                    id="discount_amount"
                                    name="discount_amount"
                                    value="{{ old('discount_amount', $voucher->discount_amount) }}"
                                    step="1000"
                                    min="0">
                                @error('discount_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="max_discount_amount" class="form-label">Giảm tối đa (đồng) (tùy chọn)</label>
                        <input type="number"
                            class="form-control @error('max_discount_amount') is-invalid @enderror"
                            id="max_discount_amount"
                            name="max_discount_amount"
                            value="{{ old('max_discount_amount', $voucher->max_discount_amount) }}"
                            step="1000"
                            min="0">
                        @error('max_discount_amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Áp dụng cho voucher giảm theo %</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expires_at" class="form-label">Ngày hết hạn <span class="text-danger">*</span></label>
                                <input type="date"
                                    class="form-control @error('expires_at') is-invalid @enderror"
                                    id="expires_at"
                                    name="expires_at"
                                    value="{{ old('expires_at', $voucher->expires_at->format('Y-m-d')) }}">
                                @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="usage_limit" class="form-label">Giới hạn số lần sử dụng (tùy chọn)</label>
                                <input type="number"
                                    class="form-control @error('usage_limit') is-invalid @enderror"
                                    id="usage_limit"
                                    name="usage_limit"
                                    value="{{ old('usage_limit', $voucher->usage_limit) }}"
                                    min="1">
                                @error('usage_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Cập nhật
                        </button>
                        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>Hủy
                        </a>
                        <button type="button"
                            class="btn btn-danger ms-auto"
                            onclick="if(confirm('Bạn có chắc muốn xóa voucher này?')) document.getElementById('delete-form').submit()">
                            <i class="bi bi-trash me-2"></i>Xóa voucher
                        </button>
                    </div>
                </form>

                <form id="delete-form" action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-light mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-bar-chart me-2"></i>Thống kê
                </h5>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2">
                        <strong>Đã sử dụng:</strong><br>
                        {{ $voucher->usage_count }}
                        @if($voucher->usage_limit)
                        / {{ $voucher->usage_limit }} lần
                        @else
                        lần (không giới hạn)
                        @endif
                    </li>
                    <li class="mb-2">
                        <strong>Trạng thái:</strong><br>
                        @if($voucher->expires_at->isPast())
                        <span class="badge bg-danger">Hết hạn</span>
                        @elseif($voucher->usage_limit && $voucher->usage_count >= $voucher->usage_limit)
                        <span class="badge bg-secondary">Đã hết lượt</span>
                        @else
                        <span class="badge bg-success">Khả dụng</span>
                        @endif
                    </li>
                    <li class="mb-2">
                        <strong>Ngày tạo:</strong><br>
                        {{ $voucher->created_at->format('d/m/Y H:i') }}
                    </li>
                    <li>
                        <strong>Cập nhật:</strong><br>
                        {{ $voucher->updated_at->format('d/m/Y H:i') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('code').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
</script>
@endsection