@extends('layouts.admin')

@section('title', 'Chỉnh sửa cụm rạp')

@section('content')
<div class="mb-4">
    <h2>Chỉnh sửa cụm rạp</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cinemas.index') }}">Cụm rạp</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.cinemas.update', $cinema) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Tên cụm rạp <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name', $cinema->name) }}"
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
                            value="{{ old('address', $cinema->address) }}">
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
                            <option value="Hà Nội" {{ old('city', $cinema->city) == 'Hà Nội' ? 'selected' : '' }}>Hà Nội</option>
                            <option value="Hồ Chí Minh" {{ old('city', $cinema->city) == 'Hồ Chí Minh' ? 'selected' : '' }}>Hồ Chí Minh</option>
                            <option value="Đà Nẵng" {{ old('city', $cinema->city) == 'Đà Nẵng' ? 'selected' : '' }}>Đà Nẵng</option>
                            <option value="Hải Phòng" {{ old('city', $cinema->city) == 'Hải Phòng' ? 'selected' : '' }}>Hải Phòng</option>
                            <option value="Cần Thơ" {{ old('city', $cinema->city) == 'Cần Thơ' ? 'selected' : '' }}>Cần Thơ</option>
                            <option value="Biên Hòa" {{ old('city', $cinema->city) == 'Biên Hòa' ? 'selected' : '' }}>Biên Hòa</option>
                            <option value="Nha Trang" {{ old('city', $cinema->city) == 'Nha Trang' ? 'selected' : '' }}>Nha Trang</option>
                            <option value="Huế" {{ old('city', $cinema->city) == 'Huế' ? 'selected' : '' }}>Huế</option>
                            <option value="Vũng Tàu" {{ old('city', $cinema->city) == 'Vũng Tàu' ? 'selected' : '' }}>Vũng Tàu</option>
                        </select>
                        @error('city')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Cập nhật
                        </button>
                        <a href="{{ route('admin.cinemas.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>Hủy
                        </a>
                        <button type="button"
                            class="btn btn-danger ms-auto"
                            onclick="if(confirm('Bạn có chắc muốn xóa cụm rạp này?')) document.getElementById('delete-form').submit()">
                            <i class="bi bi-trash me-2"></i>Xóa cụm rạp
                        </button>
                    </div>
                </form>

                <form id="delete-form" action="{{ route('admin.cinemas.destroy', $cinema) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-light">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-clock-history me-2"></i>Thông tin
                </h5>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2">
                        <strong>Ngày tạo:</strong><br>
                        {{ $cinema->created_at->format('d/m/Y H:i') }}
                    </li>
                    <li>
                        <strong>Cập nhật:</strong><br>
                        {{ $cinema->updated_at->format('d/m/Y H:i') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection