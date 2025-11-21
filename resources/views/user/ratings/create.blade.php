@extends('layouts.user')

@section('title', 'Đánh giá phim')

@push('styles')
    <style>
        .rating-container {
            background: linear-gradient(135deg, var(--card-bg), var(--card-hover));
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        .movie-info-section {
            border-bottom: 2px solid var(--border-color);
        }

        .movie-poster {
            border: 2px solid var(--border-color);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.8);
        }

        .star-rating-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .star {
            font-size: 2.5rem;
            color: #4a5568;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            background: none;
            padding: 0;
        }

        .star:hover {
            color: var(--accent-gold);
            transform: scale(1.2);
        }

        .star.active {
            color: var(--accent-gold);
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
        }

        .rating-text {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--accent-gold);
            min-width: 80px;
        }

        .review-textarea {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            resize: none;
        }

        .review-textarea:focus {
            background-color: rgba(255, 255, 255, 0.08);
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 0.25rem rgba(255, 215, 0, 0.15);
        }

        .review-textarea::placeholder {
            color: var(--text-muted);
        }

        .btn-submit-rating {
            background: linear-gradient(135deg, var(--accent-gold), #f0c000);
            color: #000;
            font-weight: 600;
            border: none;
            padding: 0.75rem 2rem;
            transition: all 0.3s ease;
        }

        .btn-submit-rating:hover {
            background: linear-gradient(135deg, #f0c000, var(--accent-gold));
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 215, 0, 0.4);
            color: #000;
        }

        .btn-cancel {
            background: var(--card-hover);
            color: var(--text-primary);
            font-weight: 600;
            border: 1px solid var(--border-color);
            padding: 0.75rem 2rem;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: var(--card-bg);
            border-color: var(--primary-color);
            color: var(--text-primary);
            transform: translateY(-2px);
        }

        .error-text {
            color: var(--primary-color);
            font-size: 0.875rem;
        }

        .char-count {
            color: var(--text-muted);
            font-size: 0.875rem;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="rating-container rounded-4 p-4 p-md-5">
                    <!-- Movie Info -->
                    <div class="movie-info-section pb-4 mb-4">
                        <div class="d-flex gap-3 gap-md-4">
                            <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="movie-poster rounded-3"
                                style="width: 100px; height: 150px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <h1 class="h3 fw-bold mb-3">{{ $movie->title }}</h1>
                                <p class="text-secondary mb-2">
                                    <i class="bi bi-clock"></i>
                                    Suất chiếu: {{ $booking->showtime->start_time->format('H:i - d/m/Y') }}
                                </p>
                                <p class="text-secondary mb-2">
                                    <i class="bi bi-geo-alt"></i>
                                    Rạp: {{ $booking->showtime->theater->cinema->name }}
                                </p>
                                <p class="text-secondary mb-0">
                                    <i class="bi bi-door-open"></i>
                                    Phòng: {{ $booking->showtime->theater->name }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Rating Form -->
                    <form action="{{ route('ratings.store', $booking->id) }}" method="POST">
                        @csrf

                        <!-- Star Rating -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold fs-5 mb-3">
                                <i class="bi bi-star-fill" style="color: var(--accent-gold);"></i>
                                Đánh giá của bạn:
                            </label>
                            <div class="star-rating-container">
                                <div id="star-rating" class="d-flex gap-1">
                                    @for ($i = 1; $i <= 10; $i++)
                                        <button type="button" data-rating="{{ $i }}" class="star">
                                            ★
                                        </button>
                                    @endfor
                                </div>
                                <span id="rating-text" class="rating-text">0/10</span>
                            </div>
                            <input type="hidden" name="rating" id="rating-input" value="" required>
                            @error('rating')
                                <div class="error-text mt-2">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Review Text -->
                        <div class="mb-4">
                            <label for="review" class="form-label fw-semibold fs-5 mb-2">
                                <i class="bi bi-chat-left-text"></i>
                                Nhận xét (tùy chọn):
                            </label>
                            <textarea name="review" id="review" rows="6" maxlength="1000" class="form-control review-textarea rounded-3"
                                placeholder="Chia sẻ cảm nhận của bạn về bộ phim...">{{ old('review') }}</textarea>
                            <div class="d-flex justify-content-between mt-2">
                                <span class="char-count">Tối đa 1000 ký tự</span>
                                <span class="char-count" id="char-counter">0/1000</span>
                            </div>
                            @error('review')
                                <div class="error-text mt-2">
                                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-submit-rating rounded-pill flex-grow-1">
                                <i class="bi bi-send"></i> Gửi đánh giá
                            </button>
                            <a href="{{ route('bookings.history') }}" class="btn btn-cancel rounded-pill flex-grow-1">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const stars = document.querySelectorAll('.star');
                const ratingInput = document.getElementById('rating-input');
                const ratingText = document.getElementById('rating-text');
                const reviewTextarea = document.getElementById('review');
                const charCounter = document.getElementById('char-counter');
                let currentRating = 0;

                // Star rating functionality
                stars.forEach(star => {
                    // Hover effect
                    star.addEventListener('mouseenter', function() {
                        const rating = parseInt(this.dataset.rating);
                        highlightStars(rating);
                        ratingText.textContent = rating + '/10';
                    });

                    // Click to select
                    star.addEventListener('click', function(e) {
                        e.preventDefault();
                        currentRating = parseInt(this.dataset.rating);
                        ratingInput.value = currentRating;
                        highlightStars(currentRating);
                        ratingText.textContent = currentRating + '/10';
                    });
                });

                // Mouse leave - reset to current rating
                document.getElementById('star-rating').addEventListener('mouseleave', function() {
                    highlightStars(currentRating);
                    ratingText.textContent = currentRating > 0 ? currentRating + '/10' : '0/10';
                });

                function highlightStars(rating) {
                    stars.forEach((star, index) => {
                        if (index < rating) {
                            star.classList.add('active');
                        } else {
                            star.classList.remove('active');
                        }
                    });
                }

                // Character counter for review
                reviewTextarea.addEventListener('input', function() {
                    const length = this.value.length;
                    charCounter.textContent = length + '/1000';
                });

                // Initialize char counter
                charCounter.textContent = reviewTextarea.value.length + '/1000';
            });
        </script>
    @endpush
@endsection
