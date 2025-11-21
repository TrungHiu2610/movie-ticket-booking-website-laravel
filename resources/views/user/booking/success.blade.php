@extends('layouts.user')

@section('title', 'Đặt vé thành công')

@section('content')
<div class="success-container">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="success-card">
                    <div class="success-icon">
                        <div class="checkmark-circle">
                            <i class="bi bi-check-lg"></i>
                        </div>
                    </div>

                    <h2>Đặt vé thành công!</h2>
                    <p class="success-message">Cảm ơn bạn đã đặt vé. Vé điện tử đã được gửi về email của bạn.</p>

                    <div class="booking-info">
                        <div class="booking-code">
                            <span class="label">Mã đặt vé</span>
                            <span class="code">{{ $booking->booking_code }}</span>
                        </div>

                        <div class="movie-info">
                            <h4><i class="bi bi-film me-2"></i>{{ $booking->showtime->movie->title }}</h4>

                            <div class="info-grid">
                                <div class="info-item">
                                    <i class="bi bi-geo-alt"></i>
                                    <div>
                                        <div class="info-label">Rạp chiếu</div>
                                        <div class="info-value">{{ $booking->showtime->theater->cinema->name }}</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <i class="bi bi-door-open"></i>
                                    <div>
                                        <div class="info-label">Phòng chiếu</div>
                                        <div class="info-value">{{ $booking->showtime->theater->name }}</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <i class="bi bi-calendar-event"></i>
                                    <div>
                                        <div class="info-label">Ngày giờ chiếu</div>
                                        <div class="info-value">
                                            {{ \Carbon\Carbon::parse($booking->showtime->start_time)->format('H:i - d/m/Y') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <i class="bi bi-alarm"></i>
                                    <div>
                                        <div class="info-label">Thời lượng</div>
                                        <div class="info-value">{{ $booking->showtime->movie->duration }} phút</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tickets-section">
                            <h5><i class="bi bi-ticket-perforated me-2"></i>Vé của bạn</h5>
                            <div class="tickets-grid">
                                @foreach($booking->tickets as $ticket)
                                <div class="ticket-item">
                                    <div class="seat-name">{{ $ticket->seat->row_char }}{{ $ticket->seat->column_number }}</div>
                                    <div class="seat-price">{{ number_format($ticket->price) }}đ</div>
                                </div>
                                @endforeach
                            </div>

                            <div class="total-amount">
                                <span>Tổng cộng</span>
                                <span>{{ number_format($booking->total_amount) }}đ</span>
                            </div>
                        </div>

                        <div class="qr-section">
                            <div class="qr-placeholder">
                                <i class="bi bi-qr-code"></i>
                                <p>Mã QR vé đã được gửi về email</p>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <a href="{{ route('bookings.history') }}" class="btn-history">
                            <i class="bi bi-clock-history me-2"></i>
                            Xem lịch sử đặt vé
                        </a>
                        <a href="{{ route('home') }}" class="btn-home">
                            <i class="bi bi-house me-2"></i>
                            Về trang chủ
                        </a>
                    </div>

                    <div class="reminder-note">
                        <i class="bi bi-info-circle"></i>
                        <div>
                            <strong>Lưu ý quan trọng:</strong>
                            <ul>
                                <li>Vui lòng đến rạp trước giờ chiếu ít nhất 15 phút</li>
                                <li>Xuất trình mã QR (trong email) tại quầy để nhận vé</li>
                                <li>Kiểm tra kỹ thông tin vé trước khi đến rạp</li>
                                <li>Liên hệ hotline nếu có bất kỳ vấn đề gì</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .success-container {
        min-height: calc(100vh - 200px);
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        padding-top: 80px;
    }

    .success-card {
        background: var(--card-bg);
        border-radius: 20px;
        padding: 50px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        text-align: center;
    }

    .success-icon {
        margin-bottom: 25px;
    }

    .checkmark-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4caf50, #8bc34a);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        animation: scaleIn 0.5s ease-out;
        box-shadow: 0 10px 30px rgba(76, 175, 80, 0.4);
    }

    .checkmark-circle i {
        font-size: 60px;
        color: #fff;
        font-weight: bold;
    }

    @keyframes scaleIn {
        0% {
            transform: scale(0);
            opacity: 0;
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .success-card h2 {
        color: #fff;
        font-size: 32px;
        margin-bottom: 15px;
        font-weight: 700;
    }

    .success-message {
        color: rgba(255, 255, 255, 0.8);
        font-size: 18px;
        margin-bottom: 35px;
    }

    .booking-info {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        padding: 30px;
        margin: 30px 0;
        text-align: left;
    }

    .booking-code {
        background: linear-gradient(135deg, rgba(229, 9, 20, 0.2), rgba(214, 10, 94, 0.2));
        border: 2px solid var(--primary-color);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        margin-bottom: 30px;
    }

    .booking-code .label {
        display: block;
        color: rgba(255, 255, 255, 0.7);
        font-size: 14px;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .booking-code .code {
        display: block;
        color: var(--primary-color);
        font-size: 28px;
        font-weight: 700;
        letter-spacing: 2px;
    }

    .movie-info h4 {
        color: #fff;
        font-size: 24px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-item {
        display: flex;
        gap: 15px;
        background: rgba(255, 255, 255, 0.05);
        padding: 15px;
        border-radius: 10px;
    }

    .info-item i {
        font-size: 24px;
        color: var(--primary-color);
        margin-top: 5px;
    }

    .info-label {
        color: rgba(255, 255, 255, 0.6);
        font-size: 13px;
        margin-bottom: 5px;
    }

    .info-value {
        color: #fff;
        font-size: 16px;
        font-weight: 600;
    }

    .tickets-section {
        border-top: 2px solid rgba(255, 255, 255, 0.1);
        padding-top: 25px;
    }

    .tickets-section h5 {
        color: #fff;
        font-size: 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .tickets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .ticket-item {
        background: linear-gradient(135deg, rgba(229, 9, 20, 0.3), rgba(214, 10, 94, 0.3));
        border: 2px solid var(--primary-color);
        border-radius: 10px;
        padding: 15px;
        text-align: center;
    }

    .seat-name {
        color: #fff;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .seat-price {
        color: rgba(255, 255, 255, 0.8);
        font-size: 15px;
    }

    .total-amount {
        display: flex;
        justify-content: space-between;
        padding: 20px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        font-size: 20px;
        font-weight: 700;
        color: #fff;
    }

    .total-amount span:last-child {
        color: var(--primary-color);
    }

    .qr-section {
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid rgba(255, 255, 255, 0.1);
    }

    .qr-placeholder {
        background: rgba(255, 255, 255, 0.05);
        border: 2px dashed rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 40px;
        text-align: center;
    }

    .qr-placeholder i {
        font-size: 80px;
        color: rgba(255, 255, 255, 0.3);
        margin-bottom: 15px;
    }

    .qr-placeholder p {
        color: rgba(255, 255, 255, 0.6);
        margin: 0;
        font-size: 16px;
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        margin: 35px 0 25px;
    }

    .action-buttons a {
        flex: 1;
        padding: 16px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .btn-history {
        background: linear-gradient(135deg, var(--primary-color), #d60a5e);
        color: #fff;
        box-shadow: 0 5px 20px rgba(229, 9, 20, 0.3);
    }

    .btn-history:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(229, 9, 20, 0.5);
        color: #fff;
    }

    .btn-home {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .btn-home:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        color: #fff;
    }

    .reminder-note {
        background: rgba(33, 150, 243, 0.1);
        border-left: 4px solid #2196f3;
        border-radius: 8px;
        padding: 20px;
        text-align: left;
        display: flex;
        gap: 15px;
    }

    .reminder-note i {
        font-size: 24px;
        color: #2196f3;
        margin-top: 5px;
    }

    .reminder-note strong {
        color: #fff;
        display: block;
        margin-bottom: 10px;
    }

    .reminder-note ul {
        margin: 0;
        padding-left: 20px;
        color: rgba(255, 255, 255, 0.8);
    }

    .reminder-note li {
        margin-bottom: 6px;
    }

    @media (max-width: 768px) {
        .success-card {
            padding: 30px 20px;
        }

        .success-card h2 {
            font-size: 26px;
        }

        .checkmark-circle {
            width: 100px;
            height: 100px;
        }

        .checkmark-circle i {
            font-size: 50px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .tickets-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        }

        .action-buttons {
            flex-direction: column;
        }

        .booking-code .code {
            font-size: 22px;
        }
    }
</style>
@endsection