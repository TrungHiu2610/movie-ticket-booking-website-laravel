@extends('layouts.user')

@section('title', 'Lịch sử đặt vé')

@section('content')
    <div class="history-container">
        <div class="container py-5">
            <div class="history-header">
                <h1><i class="bi bi-clock-history me-3"></i>Lịch sử đặt vé</h1>
                <p>Xem lại các vé bạn đã đặt</p>
            </div>

            @if ($bookings->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-ticket-perforated"></i>
                    <h3>Chưa có vé nào</h3>
                    <p>Bạn chưa đặt vé nào. Hãy chọn phim và đặt vé ngay!</p>
                    <a href="{{ route('movies.index') }}" class="btn-browse">
                        <i class="bi bi-film me-2"></i>
                        Xem phim đang chiếu
                    </a>
                </div>
            @else
                <div class="bookings-list">
                    @foreach ($bookings as $booking)
                        <div class="booking-card">
                            <div class="booking-header">
                                <div class="booking-code-badge">
                                    <i class="bi bi-qr-code me-2"></i>
                                    {{ $booking->booking_code }}
                                </div>
                                <div class="booking-status {{ $booking->booking_status }}">
                                    @switch($booking->booking_status)
                                        @case('confirmed')
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            Đã xác nhận
                                        @break

                                        @case('pending')
                                            <i class="bi bi-clock-fill me-1"></i>
                                            Chờ xác nhận
                                        @break

                                        @case('cancelled')
                                            <i class="bi bi-x-circle-fill me-1"></i>
                                            Đã hủy
                                        @break
                                    @endswitch
                                </div>
                            </div>

                            <div class="booking-body">
                                <div class="movie-section">
                                    <div class="movie-poster">
                                        <img src="{{ $booking->showtime->movie->poster_url ?? 'https://via.placeholder.com/200x300' }}"
                                            alt="{{ $booking->showtime->movie->title }}">
                                    </div>
                                    <div class="movie-details">
                                        <h4>{{ $booking->showtime->movie->title }}</h4>

                                        <div class="detail-row">
                                            <i class="bi bi-geo-alt"></i>
                                            <span>{{ $booking->showtime->theater->cinema->name }}</span>
                                        </div>

                                        <div class="detail-row">
                                            <i class="bi bi-door-open"></i>
                                            <span>{{ $booking->showtime->theater->name }}</span>
                                        </div>

                                        <div class="detail-row">
                                            <i class="bi bi-calendar-event"></i>
                                            <span>{{ \Carbon\Carbon::parse($booking->showtime->start_time)->format('H:i - d/m/Y') }}</span>
                                        </div>

                                        <div class="detail-row">
                                            <i class="bi bi-alarm"></i>
                                            <span>{{ $booking->showtime->movie->duration }} phút</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="tickets-section">
                                    <h6><i class="bi bi-ticket-perforated me-2"></i>Ghế đã đặt:</h6>
                                    <div class="seats-badges">
                                        @foreach ($booking->tickets as $ticket)
                                            <span
                                                class="seat-badge">{{ $ticket->seat->row_char }}{{ $ticket->seat->column_number }}</span>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="price-section">
                                    <div class="price-row">
                                        <span>Số lượng vé:</span>
                                        <span>{{ $booking->tickets->count() }} vé</span>
                                    </div>
                                    <div class="price-row total">
                                        <span>Tổng tiền:</span>
                                        <span>{{ number_format($booking->total_amount) }}đ</span>
                                    </div>
                                </div>
                            </div>

                            <div class="booking-footer">
                                <div class="booking-time">
                                    <i class="bi bi-clock me-1"></i>
                                    Đặt lúc {{ $booking->created_at->format('H:i - d/m/Y') }}
                                    @if ($booking->is_checked_in)
                                        <span class="ms-3 text-success">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            Đã soát vé
                                        </span>
                                    @endif
                                </div>
                                <div class="booking-actions">
                                    @if ($booking->status === 'confirmed' && \Carbon\Carbon::parse($booking->showtime->start_time)->isFuture())
                                        <button class="btn-action btn-qr" data-booking-id="{{ $booking->id }}">
                                            <i class="bi bi-qr-code-scan me-1"></i>
                                            Xem QR
                                        </button>
                                    @endif

                                    @if ($booking->canBeRefunded())
                                        <button class="btn-action btn-refund" onclick="requestRefund({{ $booking->id }})">
                                            <i class="bi bi-arrow-return-left me-1"></i>
                                            Yêu cầu hoàn tiền
                                        </button>
                                    @endif

                                    @if ($booking->canBeRated())
                                        <a href="{{ route('ratings.create', $booking->id) }}"
                                            class="btn-action btn-rating">
                                            <i class="bi bi-star me-1"></i>
                                            Đánh giá
                                        </a>
                                    @elseif($booking->rating)
                                        <span class="btn-action btn-rated">
                                            <i class="bi bi-star-fill me-1"></i>
                                            Đã đánh giá {{ $booking->rating->rating }}/10
                                        </span>
                                    @endif

                                    <a href="{{ route('booking.success', $booking->id) }}" class="btn-action btn-detail">
                                        <i class="bi bi-eye me-1"></i>
                                        Chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pagination-wrapper">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- QR Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-qr-code-scan me-2"></i>
                        Mã QR vé của bạn
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="qr-code-display">
                        <div class="qr-placeholder">
                            <i class="bi bi-qr-code"></i>
                        </div>
                        <p class="mt-3">Xuất trình mã QR này tại quầy để nhận vé</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .history-container {
            min-height: calc(100vh - 200px);
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            padding-top: 100px;
        }

        .history-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .history-header h1 {
            color: #fff;
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .history-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 18px;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: var(--card-bg);
            border-radius: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        .empty-state i {
            font-size: 100px;
            color: rgba(255, 255, 255, 0.2);
            margin-bottom: 25px;
        }

        .empty-state h3 {
            color: #fff;
            font-size: 28px;
            margin-bottom: 15px;
        }

        .empty-state p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 16px;
            margin-bottom: 30px;
        }

        .btn-browse {
            display: inline-flex;
            align-items: center;
            padding: 14px 32px;
            background: linear-gradient(135deg, var(--primary-color), #d60a5e);
            color: #fff;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(229, 9, 20, 0.3);
        }

        .btn-browse:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(229, 9, 20, 0.5);
            color: #fff;
        }

        .bookings-list {
            display: flex;
            flex-direction: column;
            gap: 25px;
            margin-bottom: 40px;
        }

        .booking-card {
            background: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .booking-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .booking-code-badge {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 18px;
            letter-spacing: 1px;
        }

        .booking-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .booking-status.confirmed {
            background: rgba(76, 175, 80, 0.2);
            color: #4caf50;
            border: 1px solid #4caf50;
        }

        .booking-status.pending {
            background: rgba(255, 152, 0, 0.2);
            color: #ff9800;
            border: 1px solid #ff9800;
        }

        .booking-status.cancelled {
            background: rgba(244, 67, 54, 0.2);
            color: #f44336;
            border: 1px solid #f44336;
        }

        .booking-body {
            padding: 25px;
        }

        .movie-section {
            display: flex;
            gap: 25px;
            margin-bottom: 25px;
        }

        .movie-poster {
            width: 120px;
            height: 180px;
            border-radius: 10px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .movie-poster img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .movie-details {
            flex: 1;
        }

        .movie-details h4 {
            color: #fff;
            font-size: 24px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .detail-row {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 10px;
        }

        .detail-row i {
            color: var(--primary-color);
            font-size: 18px;
            width: 20px;
        }

        .tickets-section {
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .tickets-section h6 {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 12px;
            font-size: 15px;
        }

        .seats-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .seat-badge {
            background: linear-gradient(135deg, rgba(229, 9, 20, 0.3), rgba(214, 10, 94, 0.3));
            border: 1px solid var(--primary-color);
            color: #fff;
            padding: 6px 14px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 13px;
        }

        .price-section {
            border-top: 2px solid rgba(255, 255, 255, 0.1);
            padding-top: 15px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            color: rgba(255, 255, 255, 0.8);
        }

        .price-row.total {
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            margin-top: 8px;
        }

        .price-row.total span:last-child {
            color: var(--primary-color);
        }

        .booking-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            background: rgba(255, 255, 255, 0.03);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .booking-time {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        .booking-actions {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .btn-qr {
            background: linear-gradient(135deg, var(--primary-color), #d60a5e);
            color: #fff;
        }

        .btn-qr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(229, 9, 20, 0.4);
        }

        .btn-detail {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-detail:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        .btn-rating {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: #000;
        }

        .btn-rating:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
            color: #000;
        }

        .btn-rated {
            background: rgba(255, 215, 0, 0.2);
            color: #FFD700;
            border: 1px solid #FFD700;
            cursor: default;
        }

        .btn-rated:hover {
            transform: none;
        }

        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 40px;
        }

        /* Modal Styles */
        .modal-content {
            background: var(--card-bg);
            color: #fff;
            border: none;
            border-radius: 15px;
        }

        .modal-header {
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .modal-title {
            font-size: 20px;
            font-weight: 600;
        }

        .btn-close {
            filter: invert(1);
        }

        .qr-code-display {
            padding: 20px;
        }

        .qr-placeholder {
            width: 250px;
            height: 250px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.05);
            border: 2px dashed rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-placeholder i {
            font-size: 100px;
            color: rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 768px) {
            .history-header h1 {
                font-size: 32px;
            }

            .booking-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            .movie-section {
                flex-direction: column;
            }

            .movie-poster {
                width: 100%;
                height: 300px;
            }

            .booking-footer {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .booking-actions {
                width: 100%;
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <script>
        // QR Modal
        document.querySelectorAll('.btn-qr').forEach(btn => {
            btn.addEventListener('click', function() {
                const bookingId = this.dataset.bookingId;
                const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
                qrModal.show();
            });
        });

        // Refund Request
        function requestRefund(bookingId) {
            if (!confirm('Bạn có chắc chắn muốn yêu cầu hoàn tiền cho đặt vé này?')) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/bookings/${bookingId}/refund-request`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endsection
