@extends('layouts.admin')

@section('title', 'Thêm loại ghế mới')

@section('content')
<div class="mb-4">
    <h2 class="mb-1">Thêm loại ghế mới</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.seat-types.index') }}">Loại ghế</a></li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.seat-types.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Tên loại ghế <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
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
                            value="{{ old('surcharge', 0) }}"
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

                    <div class="alert alert-info">
                        <i class="bi bi-lightbulb"></i> <strong>Lưu ý:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Giá vé cuối cùng = Giá cơ bản (suất chiếu) + Phụ thu loại ghế + Phụ thu khác</li>
                            <li>VD: Ghế Standard thường có phụ thu = 0đ, Ghế VIP có phụ thu 45,000đ</li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Lưu loại ghế
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
                    <i class="bi bi-question-circle text-primary"></i> Hướng dẫn
                </h5>
                <hr>
                <h6>Các loại ghế phổ biến:</h6>
                <ul class="small">
                    <li><strong>Standard:</strong> Ghế thường (0đ)</li>
                    <li><strong>VIP:</strong> Ghế VIP (+45,000đ)</li>
                    <li><strong>Couple:</strong> Ghế đôi (+60,000đ)</li>
                    <li><strong>Premium:</strong> Ghế cao cấp (+80,000đ)</li>
                    <li><strong>Deluxe:</strong> Ghế deluxe (+100,000đ)</li>
                </ul>

                <hr>

                <h6>Quy tắc đặt tên:</h6>
                <ul class="small mb-0">
                    <li>Nên đặt tên ngắn gọn, dễ hiểu</li>
                    <li>Có thể dùng tiếng Anh hoặc tiếng Việt</li>
                    <li>Tên phải là duy nhất trong hệ thống</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection