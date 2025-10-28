@extends('layouts.admin')

@section('title', 'Thêm voucher mới')

@section('content')
<div class="mb-4">
    <h2>Thêm voucher mới</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.vouchers.index') }}">Voucher</a></li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.vouchers.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="code" class="form-label">Mã voucher <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control font-monospace @error('code') is-invalid @enderror"
                            id="code"
                            name="code"
                            value="{{ old('code') }}"
                            placeholder="Ví dụ: SUMMER2024, NEWUSER50..."
                            style="text-transform: uppercase"
                            autofocus>
                        @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Mã voucher sẽ tự động chuyển thành chữ IN HOA</small>
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
                                    value="{{ old('discount_percentage') }}"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    placeholder="Ví dụ: 10, 20, 50">
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
                                    value="{{ old('discount_amount') }}"
                                    step="1000"
                                    min="0"
                                    placeholder="Ví dụ: 50000, 100000">
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
                            value="{{ old('max_discount_amount') }}"
                            step="1000"
                            min="0"
                            placeholder="Ví dụ: 200000">
                        @error('max_discount_amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Áp dụng cho voucher giảm theo %. Ví dụ: giảm 20% tối đa 200,000đ</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expires_at" class="form-label">Ngày hết hạn <span class="text-danger">*</span></label>
                                <input type="date"
                                    class="form-control @error('expires_at') is-invalid @enderror"
                                    id="expires_at"
                                    name="expires_at"
                                    value="{{ old('expires_at') }}"
                                    min="{{ date('Y-m-d') }}">
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
                                    value="{{ old('usage_limit') }}"
                                    min="1"
                                    placeholder="Để trống = không giới hạn">
                                @error('usage_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Tạo voucher
                        </button>
                        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-light">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-info-circle me-2"></i>Hướng dẫn
                </h5>
                <ul class="mb-0 small">
                    <li>Mã voucher nên ngắn gọn và dễ nhớ</li>
                    <li>Chọn giảm theo % HOẶC giảm cố định</li>
                    <li>Giảm tối đa chỉ áp dụng cho voucher giảm %</li>
                    <li>Giới hạn số lần = tổng số người có thể dùng</li>
                    <li>Để trống giới hạn = không giới hạn</li>
                </ul>
            </div>
        </div>

        <div class="card bg-warning bg-opacity-10 border-warning mt-3">
            <div class="card-body">
                <h6 class="card-title text-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>Ví dụ
                </h6>
                <ul class="mb-0 small">
                    <li><strong>SUMMER20:</strong> Giảm 20%, tối đa 100,000đ</li>
                    <li><strong>NEWUSER50:</strong> Giảm 50,000đ cho người dùng mới</li>
                    <li><strong>VIP100:</strong> Giảm 100,000đ, giới hạn 50 lần</li>
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