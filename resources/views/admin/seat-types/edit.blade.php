@extends('layouts.admin')

@section('title', 'Chỉnh sửa loại ghế')

@section('content')
<div class="mb-4">
    <h2 class="mb-1">Chỉnh sửa loại ghế</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.seat-types.index') }}">Loại ghế</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa: {{ $seatType->name }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.seat-types.update', $seatType) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Tên loại ghế <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name', $seatType->name) }}"
                            placeholder="VD: Standard, VIP, Couple"
                            required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> Tên loại ghế phải là duy nhất
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="surcharge" class="form-label">
                            Phụ thu (đồng) <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                            class="form-control @error('surcharge') is-invalid @enderror"
                            id="surcharge"
                            name="surcharge"
                            value="{{ old('surcharge', $seatType->surcharge) }}"
                            min="0"
                            step="1000"
                            placeholder="VD: 45000"
                            required>
                        @error('surcharge')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> Số tiền phụ thu thêm so với giá vé cơ bản. Nhập 0 nếu không có phụ thu.
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> <strong>Lưu ý:</strong>
                        Loại ghế này đang được sử dụng bởi <strong>{{ $seatType->seats()->count() }} ghế</strong>.
                        Thay đổi phụ thu sẽ ảnh hưởng đến giá vé của tất cả ghế này trong các suất chiếu mới.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Cập nhật
                        </button>
                        <a href="{{ route('admin.seat-types.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm bg-light">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-info-circle text-primary"></i> Thông tin
                </h5>
                <hr>
                <dl class="row mb-0">
                    <dt class="col-sm-5">ID:</dt>
                    <dd class="col-sm-7">{{ $seatType->id }}</dd>

                    <dt class="col-sm-5">Số ghế:</dt>
                    <dd class="col-sm-7">
                        <span class="badge bg-info text-dark">
                            {{ $seatType->seats()->count() }} ghế
                        </span>
                    </dd>

                    <dt class="col-sm-5">Tạo lúc:</dt>
                    <dd class="col-sm-7">{{ $seatType->created_at->format('d/m/Y H:i') }}</dd>

                    <dt class="col-sm-5">Cập nhật:</dt>
                    <dd class="col-sm-7">{{ $seatType->updated_at->format('d/m/Y H:i') }}</dd>
                </dl>
            </div>
        </div>

        <div class="card shadow-sm bg-light mt-3">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-calculator text-success"></i> Ví dụ tính giá
                </h5>
                <hr>
                <p class="small mb-2">Giả sử giá suất chiếu: <strong>100,000đ</strong></p>
                <ul class="small mb-0">
                    <li>Ghế Standard (0đ): <strong>100,000đ</strong></li>
                    <li>Ghế VIP (45,000đ): <strong>145,000đ</strong></li>
                    <li>Ghế {{ $seatType->name }} ({{ number_format($seatType->surcharge, 0, ',', '.') }}đ):
                        <strong>{{ number_format(100000 + $seatType->surcharge, 0, ',', '.') }}đ</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection