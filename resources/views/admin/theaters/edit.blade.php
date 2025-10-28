@extends('layouts.admin')

@section('title', 'Chỉnh sửa phòng chiếu')

@section('content')
<div class="mb-4">
    <h2>Chỉnh sửa phòng chiếu</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.theaters.index') }}">Phòng chiếu</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.theaters.update', $theater) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="cinema_id" class="form-label">Cụm rạp <span class="text-danger">*</span></label>
                        <select class="form-select @error('cinema_id') is-invalid @enderror"
                            id="cinema_id"
                            name="cinema_id">
                            <option value="">-- Chọn cụm rạp --</option>
                            @foreach($cinemas as $cinema)
                            <option value="{{ $cinema->id }}" {{ old('cinema_id', $theater->cinema_id) == $cinema->id ? 'selected' : '' }}>
                                {{ $cinema->name }} - {{ $cinema->city }}
                            </option>
                            @endforeach
                        </select>
                        @error('cinema_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Tên phòng chiếu <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name', $theater->name) }}">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="screen_type" class="form-label">Loại màn hình <span class="text-danger">*</span></label>
                        <select class="form-select @error('screen_type') is-invalid @enderror"
                            id="screen_type"
                            name="screen_type">
                            <option value="">-- Chọn loại màn hình --</option>
                            <option value="2D" {{ old('screen_type', $theater->screen_type) == '2D' ? 'selected' : '' }}>2D</option>
                            <option value="3D" {{ old('screen_type', $theater->screen_type) == '3D' ? 'selected' : '' }}>3D</option>
                            <option value="IMAX" {{ old('screen_type', $theater->screen_type) == 'IMAX' ? 'selected' : '' }}>IMAX</option>
                            <option value="4DX" {{ old('screen_type', $theater->screen_type) == '4DX' ? 'selected' : '' }}>4DX</option>
                            <option value="ScreenX" {{ old('screen_type', $theater->screen_type) == 'ScreenX' ? 'selected' : '' }}>ScreenX</option>
                        </select>
                        @error('screen_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Lưu ý:</strong> Chỉnh sửa thông tin phòng chiếu không ảnh hưởng đến ghế đã tạo.
                        Để thay đổi số lượng ghế, bạn cần xóa và tạo lại phòng chiếu.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Cập nhật
                        </button>
                        <a href="{{ route('admin.theaters.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>Hủy
                        </a>
                        <button type="button"
                            class="btn btn-danger ms-auto"
                            onclick="if(confirm('Bạn có chắc muốn xóa phòng chiếu này? Tất cả ghế sẽ bị xóa!')) document.getElementById('delete-form').submit()">
                            <i class="bi bi-trash me-2"></i>Xóa phòng chiếu
                        </button>
                    </div>
                </form>

                <form id="delete-form" action="{{ route('admin.theaters.destroy', $theater) }}" method="POST" class="d-none">
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
                    <i class="bi bi-info-circle me-2"></i>Thông tin ghế
                </h5>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2">
                        <strong>Tổng số ghế:</strong><br>
                        {{ $theater->seats->count() }} ghế
                    </li>
                    <li class="mb-2">
                        <strong>Ngày tạo:</strong><br>
                        {{ $theater->created_at->format('d/m/Y H:i') }}
                    </li>
                    <li>
                        <strong>Cập nhật:</strong><br>
                        {{ $theater->updated_at->format('d/m/Y H:i') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection