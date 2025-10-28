@extends('layouts.admin')

@section('title', 'Thêm cụm rạp mới')

@section('content')
<div class="mb-4">
    <h2>Thêm cụm rạp mới</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cinemas.index') }}">Cụm rạp</a></li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.cinemas.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Tên cụm rạp <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Ví dụ: CGV Vincom, Lotte Cinema Landmark..."
                            autofocus>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control @error('address') is-invalid @enderror"
                            id="address"
                            name="address"
                            value="{{ old('address') }}"
                            placeholder="Ví dụ: 72 Lê Thánh Tôn, Phường Bến Nghé, Quận 1">
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="city" class="form-label">Thành phố <span class="text-danger">*</span></label>
                        <select class="form-select @error('city') is-invalid @enderror"
                            id="city"
                            name="city">
                            <option value="">-- Chọn thành phố --</option>
                            <option value="Hà Nội" {{ old('city') == 'Hà Nội' ? 'selected' : '' }}>Hà Nội</option>
                            <option value="Hồ Chí Minh" {{ old('city') == 'Hồ Chí Minh' ? 'selected' : '' }}>Hồ Chí Minh</option>
                            <option value="Đà Nẵng" {{ old('city') == 'Đà Nẵng' ? 'selected' : '' }}>Đà Nẵng</option>
                            <option value="Hải Phòng" {{ old('city') == 'Hải Phòng' ? 'selected' : '' }}>Hải Phòng</option>
                            <option value="Cần Thơ" {{ old('city') == 'Cần Thơ' ? 'selected' : '' }}>Cần Thơ</option>
                            <option value="Biên Hòa" {{ old('city') == 'Biên Hòa' ? 'selected' : '' }}>Biên Hòa</option>
                            <option value="Nha Trang" {{ old('city') == 'Nha Trang' ? 'selected' : '' }}>Nha Trang</option>
                            <option value="Huế" {{ old('city') == 'Huế' ? 'selected' : '' }}>Huế</option>
                            <option value="Vũng Tàu" {{ old('city') == 'Vũng Tàu' ? 'selected' : '' }}>Vũng Tàu</option>
                        </select>
                        @error('city')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Lưu cụm rạp
                        </button>
                        <a href="{{ route('admin.cinemas.index') }}" class="btn btn-secondary">
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
                    <li>Tất cả các trường đều bắt buộc</li>
                    <li>Tên cụm rạp: tối đa 255 ký tự</li>
                    <li>Địa chỉ: tối đa 500 ký tự</li>
                    <li>Chọn thành phố phù hợp</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection