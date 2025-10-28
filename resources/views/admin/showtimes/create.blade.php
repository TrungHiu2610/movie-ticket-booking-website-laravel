@extends('layouts.admin')

@section('title', 'Thêm suất chiếu mới')

@section('content')
<div class="mb-4">
    <h2>Thêm suất chiếu mới</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.showtimes.index') }}">Lịch chiếu</a></li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.showtimes.store') }}" method="POST" id="showtimeForm">
                    @csrf

                    <!-- Chọn phim -->
                    <div class="mb-3">
                        <label for="movie_id" class="form-label">Phim <span class="text-danger">*</span></label>
                        <select class="form-select @error('movie_id') is-invalid @enderror"
                            id="movie_id"
                            name="movie_id">
                            <option value="">-- Chọn phim --</option>
                            @foreach($movies as $movie)
                            <option value="{{ $movie->id }}"
                                data-duration="{{ $movie->duration_minutes }}"
                                data-base-price="{{ $movie->base_price }}"
                                {{ old('movie_id') == $movie->id ? 'selected' : '' }}>
                                {{ $movie->title }} ({{ $movie->duration_minutes }}p) - {{ number_format($movie->base_price, 0, ',', '.') }}đ
                            </option>
                            @endforeach
                        </select>
                        @error('movie_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Chọn cụm rạp -->
                    <div class="mb-3">
                        <label for="cinema_id" class="form-label">Cụm rạp <span class="text-danger">*</span></label>
                        <select class="form-select" id="cinema_id">
                            <option value="">-- Chọn cụm rạp trước --</option>
                            @foreach($cinemas as $cinema)
                            <option value="{{ $cinema->id }}">
                                {{ $cinema->name }} - {{ $cinema->city }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Chọn phòng chiếu -->
                    <div class="mb-3">
                        <label for="theater_id" class="form-label">Phòng chiếu <span class="text-danger">*</span></label>
                        <select class="form-select @error('theater_id') is-invalid @enderror"
                            id="theater_id"
                            name="theater_id"
                            disabled>
                            <option value="">-- Chọn cụm rạp trước --</option>
                        </select>
                        @error('theater_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Thời gian chiếu -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Ngày chiếu <span class="text-danger">*</span></label>
                                <input type="date"
                                    class="form-control @error('start_time') is-invalid @enderror"
                                    id="start_date"
                                    name="start_date"
                                    value="{{ old('start_date', date('Y-m-d')) }}"
                                    min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time_only" class="form-label">Giờ chiếu <span class="text-danger">*</span></label>
                                <input type="time"
                                    class="form-control @error('start_time') is-invalid @enderror"
                                    id="start_time_only"
                                    name="start_time_only"
                                    value="{{ old('start_time_only', '19:00') }}">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="start_time" id="start_time" value="{{ old('start_time') }}">

                    @error('start_time')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <!-- Giá vé với auto-calculation -->
                    <div class="mb-3">
                        <label for="base_price" class="form-label">Giá vé cơ bản (đồng) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number"
                                class="form-control @error('base_price') is-invalid @enderror"
                                id="base_price"
                                name="base_price"
                                value="{{ old('base_price', 100000) }}"
                                step="1000"
                                min="0"
                                readonly>
                            <span class="input-group-text">đ</span>
                        </div>
                        @error('base_price')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div id="price-breakdown" class="mt-2 small text-muted" style="display: none;">
                            <i class="bi bi-calculator"></i>
                            <span id="movie-base-price">0</span>đ (giá phim)
                            + <span id="theater-surcharge">0</span>đ (phụ thu phòng)
                            = <strong id="final-price">0</strong>đ
                        </div>
                    </div>

                    <!-- Thông tin phim -->
                    <div class="alert alert-info" id="duration-info" style="display: none;">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Thời lượng:</strong> <span id="movie-duration"></span> phút<br>
                        <strong>Kết thúc dự kiến:</strong> <span id="estimated-end-time"></span>
                    </div>

                    <!-- Warning trùng lịch -->
                    <div class="alert alert-warning" id="conflict-warning" style="display: none;">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Cảnh báo trùng lịch!</strong>
                        <div id="conflict-details" class="mt-2"></div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check-circle me-2"></i>Tạo suất chiếu
                        </button>
                        <a href="{{ route('admin.showtimes.index') }}" class="btn btn-secondary">
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
                    <i class="bi bi-lightbulb me-2"></i>Tự động tính giá
                </h5>
                <hr>
                <p class="small mb-2"><strong>Giá vé tự động dựa trên:</strong></p>
                <ul class="small mb-0">
                    <li><strong>Giá cơ bản phim:</strong> Mỗi phim có giá riêng (100k-130k)</li>
                    <li><strong>Phụ thu IMAX:</strong> +60,000đ</li>
                    <li><strong>Phụ thu 3D:</strong> +40,000đ</li>
                    <li>Giá ghế VIP, cuối tuần được cộng sau</li>
                </ul>
            </div>
        </div>

        <div class="card bg-warning bg-opacity-10 border-warning mt-3">
            <div class="card-body">
                <h6 class="card-title text-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>Kiểm tra trùng lịch
                </h6>
                <p class="mb-0 small">
                    Hệ thống tự động kiểm tra và cảnh báo nếu phòng chiếu đã có suất chiếu trùng giờ.
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // All theaters data from backend
    const allTheaters = {!! json_encode($theaters->map(function($t) {
        return [
            'id' => $t->id,
            'name' => $t->name,
            'cinema_id' => $t->cinema_id,
            'screen_type' => $t->screen_type,
        ];
    })->values()) !!};

    // Combine date and time
    function updateStartTime() {
        const date = document.getElementById('start_date').value;
        const time = document.getElementById('start_time_only').value;
        if (date && time) {
            document.getElementById('start_time').value = date + ' ' + time + ':00';
            checkConflicts(); // Check conflicts when time changes
        }
    }

    // Filter theaters by cinema
    document.getElementById('cinema_id').addEventListener('change', function() {
        const cinemaId = parseInt(this.value);
        const theaterSelect = document.getElementById('theater_id');

        theaterSelect.innerHTML = '<option value="">-- Chọn phòng chiếu --</option>';

        if (cinemaId) {
            const filtered = allTheaters.filter(t => t.cinema_id === cinemaId);
            filtered.forEach(theater => {
                const option = document.createElement('option');
                option.value = theater.id;
                option.textContent = `${theater.name} (${theater.screen_type})`;
                option.setAttribute('data-screen-type', theater.screen_type);
                theaterSelect.appendChild(option);
            });
            theaterSelect.disabled = false;
        } else {
            theaterSelect.disabled = true;
        }
    });

    // Auto-calculate price when movie or theater changes
    function calculatePrice() {
        const movieSelect = document.getElementById('movie_id');
        const theaterSelect = document.getElementById('theater_id');
        const selectedMovie = movieSelect.options[movieSelect.selectedIndex];
        const selectedTheater = theaterSelect.options[theaterSelect.selectedIndex];

        let basePrice = parseInt(selectedMovie.getAttribute('data-base-price')) || 0;
        let theaterSurcharge = 0;

        if (selectedTheater && selectedTheater.value) {
            const screenType = selectedTheater.getAttribute('data-screen-type') || '';

            // Phụ thu theo loại phòng
            if (screenType.includes('IMAX')) {
                theaterSurcharge = 60000;
            } else if (screenType.includes('3D')) {
                theaterSurcharge = 40000;
            }
        }

        const finalPrice = basePrice + theaterSurcharge;

        // Update UI
        document.getElementById('base_price').value = finalPrice;
        document.getElementById('movie-base-price').textContent = basePrice.toLocaleString('vi-VN');
        document.getElementById('theater-surcharge').textContent = theaterSurcharge.toLocaleString('vi-VN');
        document.getElementById('final-price').textContent = finalPrice.toLocaleString('vi-VN');

        if (basePrice > 0) {
            document.getElementById('price-breakdown').style.display = 'block';
        }
    }

    // Calculate end time
    function updateEndTime() {
        const movieSelect = document.getElementById('movie_id');
        const selectedOption = movieSelect.options[movieSelect.selectedIndex];
        const duration = parseInt(selectedOption.getAttribute('data-duration')) || 0;

        const date = document.getElementById('start_date').value;
        const time = document.getElementById('start_time_only').value;

        if (duration && date && time) {
            document.getElementById('movie-duration').textContent = duration;

            const startDateTime = new Date(date + ' ' + time);
            const endDateTime = new Date(startDateTime.getTime() + duration * 60000);

            const endTimeStr = endDateTime.toLocaleString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });

            document.getElementById('estimated-end-time').textContent = endTimeStr;
            document.getElementById('duration-info').style.display = 'block';
        }
    }

    // Check schedule conflicts
    async function checkConflicts() {
        const theaterId = document.getElementById('theater_id').value;
        const date = document.getElementById('start_date').value;
        const time = document.getElementById('start_time_only').value;
        const movieId = document.getElementById('movie_id').value;

        if (!theaterId || !date || !time || !movieId) {
            document.getElementById('conflict-warning').style.display = 'none';
            return;
        }

        const movieSelect = document.getElementById('movie_id');
        const duration = parseInt(movieSelect.options[movieSelect.selectedIndex].getAttribute('data-duration')) || 0;

        const startDateTime = new Date(date + ' ' + time);
        const endDateTime = new Date(startDateTime.getTime() + duration * 60000);

        try {
            const response = await fetch(`/admin/showtimes/check-conflicts?theater_id=${theaterId}&start_time=${startDateTime.toISOString()}&end_time=${endDateTime.toISOString()}`);
            const data = await response.json();

            if (data.conflicts && data.conflicts.length > 0) {
                let html = '<ul class="mb-0">';
                data.conflicts.forEach(conflict => {
                    const conflictStart = new Date(conflict.start_time);
                    const conflictEnd = new Date(conflict.end_time);
                    html += `<li>${conflict.movie_title}: ${conflictStart.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})} - ${conflictEnd.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})}</li>`;
                });
                html += '</ul>';

                document.getElementById('conflict-details').innerHTML = html;
                document.getElementById('conflict-warning').style.display = 'block';
                document.getElementById('submitBtn').disabled = true;
            } else {
                document.getElementById('conflict-warning').style.display = 'none';
                document.getElementById('submitBtn').disabled = false;
            }
        } catch (error) {
            console.error('Error checking conflicts:', error);
        }
    }

    // Event listeners
    document.getElementById('start_date').addEventListener('change', function() {
        updateStartTime();
        updateEndTime();
    });

    document.getElementById('start_time_only').addEventListener('change', function() {
        updateStartTime();
        updateEndTime();
    });

    document.getElementById('movie_id').addEventListener('change', function() {
        calculatePrice();
        updateEndTime();
        checkConflicts();
    });

    document.getElementById('theater_id').addEventListener('change', function() {
        calculatePrice();
        checkConflicts();
    });

    // Initialize on page load
    updateStartTime();
</script>
@endpush
@endsection