@extends('layouts.admin')

@section('title', 'Chỉnh sửa suất chiếu')

@section('content')
<div class="mb-4">
    <h2>Chỉnh sửa suất chiếu</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.showtimes.index') }}">Lịch chiếu</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.showtimes.update', $showtime) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="movie_id" class="form-label">Phim <span class="text-danger">*</span></label>
                        <select class="form-select @error('movie_id') is-invalid @enderror"
                            id="movie_id"
                            name="movie_id">
                            <option value="">-- Chọn phim --</option>
                            @foreach($movies as $movie)
                            <option value="{{ $movie->id }}"
                                data-duration="{{ $movie->duration_minutes }}"
                                {{ old('movie_id', $showtime->movie_id) == $movie->id ? 'selected' : '' }}>
                                {{ $movie->title }} ({{ $movie->duration_minutes }} phút)
                            </option>
                            @endforeach
                        </select>
                        @error('movie_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="theater_id" class="form-label">Phòng chiếu <span class="text-danger">*</span></label>
                        <select class="form-select @error('theater_id') is-invalid @enderror"
                            id="theater_id"
                            name="theater_id">
                            <option value="">-- Chọn phòng chiếu --</option>
                            @foreach($theaters as $theater)
                            <option value="{{ $theater->id }}" {{ old('theater_id', $showtime->theater_id) == $theater->id ? 'selected' : '' }}>
                                {{ $theater->cinema->name }} - {{ $theater->name }} ({{ $theater->screen_type }})
                            </option>
                            @endforeach
                        </select>
                        @error('theater_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @php
                    $startTime = \Carbon\Carbon::parse($showtime->start_time);
                    @endphp

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Ngày chiếu <span class="text-danger">*</span></label>
                                <input type="date"
                                    class="form-control @error('start_time') is-invalid @enderror"
                                    id="start_date"
                                    name="start_date"
                                    value="{{ old('start_date', $startTime->format('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time_only" class="form-label">Giờ chiếu <span class="text-danger">*</span></label>
                                <input type="time"
                                    class="form-control @error('start_time') is-invalid @enderror"
                                    id="start_time_only"
                                    name="start_time_only"
                                    value="{{ old('start_time_only', $startTime->format('H:i')) }}">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="start_time" id="start_time" value="{{ old('start_time', $showtime->start_time) }}">

                    @error('start_time')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="mb-3">
                        <label for="base_price" class="form-label">Giá vé cơ bản (đồng) <span class="text-danger">*</span></label>
                        <input type="number"
                            class="form-control @error('base_price') is-invalid @enderror"
                            id="base_price"
                            name="base_price"
                            value="{{ old('base_price', $showtime->base_price) }}"
                            step="1000"
                            min="0">
                        @error('base_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info" id="duration-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Thời lượng phim:</strong> <span id="movie-duration">{{ $showtime->movie->duration_minutes }}</span> phút<br>
                        <strong>Thời gian kết thúc dự kiến:</strong> <span id="estimated-end-time"></span>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Cập nhật
                        </button>
                        <a href="{{ route('admin.showtimes.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>Hủy
                        </a>
                        <button type="button"
                            class="btn btn-danger ms-auto"
                            onclick="if(confirm('Bạn có chắc muốn xóa suất chiếu này?')) document.getElementById('delete-form').submit()">
                            <i class="bi bi-trash me-2"></i>Xóa suất chiếu
                        </button>
                    </div>
                </form>

                <form id="delete-form" action="{{ route('admin.showtimes.destroy', $showtime) }}" method="POST" class="d-none">
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
                        <strong>Thời gian hiện tại:</strong><br>
                        Bắt đầu: {{ $startTime->format('d/m/Y H:i') }}<br>
                        Kết thúc: {{ \Carbon\Carbon::parse($showtime->end_time)->format('d/m/Y H:i') }}
                    </li>
                    <li class="mb-2">
                        <strong>Ngày tạo:</strong><br>
                        {{ $showtime->created_at->format('d/m/Y H:i') }}
                    </li>
                    <li>
                        <strong>Cập nhật:</strong><br>
                        {{ $showtime->updated_at->format('d/m/Y H:i') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function updateStartTime() {
        const date = document.getElementById('start_date').value;
        const time = document.getElementById('start_time_only').value;
        if (date && time) {
            document.getElementById('start_time').value = date + ' ' + time + ':00';
        }
    }

    document.getElementById('start_date').addEventListener('change', updateStartTime);
    document.getElementById('start_time_only').addEventListener('change', updateStartTime);

    document.getElementById('movie_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const duration = selectedOption.getAttribute('data-duration');

        if (duration) {
            document.getElementById('movie-duration').textContent = duration;
            calculateEndTime();
        }
    });

    function calculateEndTime() {
        const date = document.getElementById('start_date').value;
        const time = document.getElementById('start_time_only').value;
        const selectedOption = document.getElementById('movie_id').options[document.getElementById('movie_id').selectedIndex];
        const duration = parseInt(selectedOption.getAttribute('data-duration') || 0);

        if (date && time && duration) {
            const startDateTime = new Date(date + ' ' + time);
            const endDateTime = new Date(startDateTime.getTime() + duration * 60000);

            const endHours = String(endDateTime.getHours()).padStart(2, '0');
            const endMinutes = String(endDateTime.getMinutes()).padStart(2, '0');
            const endDate = endDateTime.toLocaleDateString('vi-VN');

            document.getElementById('estimated-end-time').textContent = endDate + ' ' + endHours + ':' + endMinutes;
        }
    }

    document.getElementById('start_date').addEventListener('change', calculateEndTime);
    document.getElementById('start_time_only').addEventListener('change', calculateEndTime);

    // Initialize
    updateStartTime();
    calculateEndTime();
</script>
@endsection