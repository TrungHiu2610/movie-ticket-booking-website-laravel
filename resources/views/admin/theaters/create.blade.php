@extends('layouts.admin')

@section('title', 'Thêm phòng chiếu mới')

@section('content')
<div class="mb-4">
    <h2>Thêm phòng chiếu mới</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.theaters.index') }}">Phòng chiếu</a></li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.theaters.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="cinema_id" class="form-label">Cụm rạp <span class="text-danger">*</span></label>
                        <select class="form-select @error('cinema_id') is-invalid @enderror"
                            id="cinema_id"
                            name="cinema_id">
                            <option value="">-- Chọn cụm rạp --</option>
                            @foreach($cinemas as $cinema)
                            <option value="{{ $cinema->id }}" {{ old('cinema_id') == $cinema->id ? 'selected' : '' }}>
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
                            value="{{ old('name') }}"
                            placeholder="Ví dụ: Phòng 1, Phòng IMAX, Phòng VIP...">
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
                            <option value="2D" {{ old('screen_type') == '2D' ? 'selected' : '' }}>2D</option>
                            <option value="3D" {{ old('screen_type') == '3D' ? 'selected' : '' }}>3D</option>
                            <option value="IMAX" {{ old('screen_type') == 'IMAX' ? 'selected' : '' }}>IMAX</option>
                            <option value="4DX" {{ old('screen_type') == '4DX' ? 'selected' : '' }}>4DX</option>
                            <option value="ScreenX" {{ old('screen_type') == 'ScreenX' ? 'selected' : '' }}>ScreenX</option>
                        </select>
                        @error('screen_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rows" class="form-label">Số hàng ghế <span class="text-danger">*</span></label>
                                <input type="number"
                                    class="form-control @error('rows') is-invalid @enderror"
                                    id="rows"
                                    name="rows"
                                    value="{{ old('rows', 10) }}"
                                    min="1"
                                    max="26"
                                    placeholder="1-26">
                                @error('rows')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Hàng ghế sẽ được đặt tên từ A-Z (tối đa 26 hàng)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="columns" class="form-label">Số cột ghế <span class="text-danger">*</span></label>
                                <input type="number"
                                    class="form-control @error('columns') is-invalid @enderror"
                                    id="columns"
                                    name="columns"
                                    value="{{ old('columns', 15) }}"
                                    min="1"
                                    max="30"
                                    placeholder="1-30">
                                @error('columns')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Cột ghế được đánh số từ 1 (tối đa 30 cột)</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="seat_type_id" class="form-label">Loại ghế <span class="text-danger">*</span></label>
                        <select class="form-select @error('seat_type_id') is-invalid @enderror"
                            id="seat_type_id"
                            name="seat_type_id">
                            <option value="">-- Chọn loại ghế --</option>
                            @foreach($seatTypes as $seatType)
                            <option value="{{ $seatType->id }}" {{ old('seat_type_id') == $seatType->id ? 'selected' : '' }}>
                                {{ $seatType->name }} - {{ number_format($seatType->base_price) }}đ
                            </option>
                            @endforeach
                        </select>
                        @error('seat_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Tất cả ghế trong phòng sẽ có cùng loại ghế này</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Lưu ý:</strong> Hệ thống sẽ tự động tạo ghế dựa vào số hàng và cột bạn nhập.
                        Ví dụ: 10 hàng x 15 cột = 150 ghế (từ A1 đến J15)
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Tạo phòng chiếu & ghế
                        </button>
                        <a href="{{ route('admin.theaters.index') }}" class="btn btn-secondary">
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
                    <li>Chọn cụm rạp trước</li>
                    <li>Tên phòng nên ngắn gọn: "Phòng 1", "IMAX"...</li>
                    <li>Số hàng: 1-26 (A-Z)</li>
                    <li>Số cột: 1-30</li>
                    <li>Ghế sẽ được tạo tự động theo ma trận</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection