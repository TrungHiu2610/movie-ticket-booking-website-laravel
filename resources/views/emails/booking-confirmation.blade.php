<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Xác nhận đặt vé</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #ddd;
        }

        .header {
            background: #e50914;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
        }

        .qr-box {
            text-align: center;
            padding: 20px;
            background: #f9f9f9;
            margin: 20px 0;
        }

        .qr-box img {
            max-width: 200px;
        }

        .code {
            font-size: 20px;
            font-weight: bold;
            color: #e50914;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .label {
            font-weight: bold;
            width: 150px;
        }

        .seats {
            background: #f5f5f5;
            padding: 15px;
            margin: 20px 0;
        }

        .total {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            padding: 15px;
            background: #f5f5f5;
        }

        .note {
            background: #fff3cd;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background: #f5f5f5;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Xác nhận đặt vé</h2>
            <p>UniCine</p>
        </div>

        <div class="content">
            <p>Xin chào <strong>{{ $user->name }}</strong>,</p>
            <p>Cảm ơn bạn đã đặt vé. Đơn hàng của bạn đã được xác nhận.</p>

            <div class="qr-box">
                <img src="{{ asset($booking->qr_code_url) }}" alt="QR Code">
                <div class="code">{{ $booking->booking_code }}</div>
                <p>Vui lòng xuất trình mã này khi đến rạp</p>
            </div>

            <table>
                <tr>
                    <td class="label">Phim</td>
                    <td>{{ $movie->title }}</td>
                </tr>
                <tr>
                    <td class="label">Rạp</td>
                    <td>{{ $cinema->name }}</td>
                </tr>
                <tr>
                    <td class="label">Phòng</td>
                    <td>{{ $theater->name }}</td>
                </tr>
                <tr>
                    <td class="label">Thời gian</td>
                    <td>{{ $showtime->start_time->format('H:i - d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Số vé</td>
                    <td>{{ $tickets->count() }}</td>
                </tr>
            </table>

            <div class="seats">
                <strong>Ghế đã đặt:</strong><br>
                @foreach ($tickets as $ticket)
                    {{ $ticket->seat->row_char }}{{ $ticket->seat->column_number }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </div>

            <div class="total">
                Tổng tiền: {{ number_format($booking->total_amount, 0, ',', '.') }}đ
            </div>

            <div class="note">
                <strong>Lưu ý:</strong>
                <ul>
                    <li>Có mặt trước 15 phút</li>
                    <li>Mang theo mã QR để soát vé</li>
                    <li>Vé không hoàn lại sau khi soát</li>
                    <li>Hotline: 1900 xxxx</li>
                </ul>
            </div>

            <p>Chúc bạn xem phim vui vẻ!</p>
        </div>

        <div class="footer">
            <p>UniCine - Email: support@unicine.vn | Hotline: 1900 xxxx</p>
            <p>&copy; {{ date('Y') }} UniCine</p>
        </div>
    </div>
</body>

</html>
body {
font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
background-color: #f4f4f4;
margin: 0;
padding: 20px;
}

.container {
max-width: 600px;
margin: 0 auto;
background: white;
border-radius: 10px;
overflow: hidden;
box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.header {
background: linear-gradient(135deg, #E50914, #B20710);
color: white;
padding: 30px;
text-align: center;
}

.header h1 {
margin: 0;
font-size: 28px;
}

.content {
padding: 30px;
}

.qr-section {
text-align: center;
padding: 20px;
background: #f9f9f9;
border-radius: 8px;
margin: 20px 0;
}

.qr-section img {
max-width: 250px;
border: 3px solid #E50914;
border-radius: 8px;
padding: 10px;
background: white;
}

.booking-code {
font-size: 24px;
font-weight: bold;
color: #E50914;
margin: 15px 0;
letter-spacing: 2px;
}

.details {
margin: 20px 0;
}

.detail-item {
padding: 12px 0;
border-bottom: 1px solid #eee;
}

.detail-label {
font-weight: bold;
color: #666;
display: inline-block;
width: 120px;
}

.detail-value {
color: #333;
}

.seats-list {
background: #f9f9f9;
padding: 15px;
border-radius: 8px;
margin: 10px 0;
}

.seat-item {
display: inline-block;
background: #E50914;
color: white;
padding: 5px 12px;
border-radius: 4px;
margin: 3px;
font-size: 14px;
}

.total-amount {
background: #E50914;
color: white;
padding: 15px;
border-radius: 8px;
text-align: center;
font-size: 24px;
font-weight: bold;
margin: 20px 0;
}

.important-note {
background: #fff3cd;
border-left: 4px solid #ffc107;
padding: 15px;
margin: 20px 0;
border-radius: 4px;
}

.footer {
background: #f9f9f9;
padding: 20px;
text-align: center;
color: #666;
font-size: 14px;
}
</style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🎬 XÁC NHẬN ĐẶT VÉ THÀNH CÔNG</h1>
            <p>UniCine - Trải nghiệm điện ảnh đỉnh cao</p>
        </div>

        <div class="content">
            <p>Xin chào <strong>{{ $user->name }}</strong>,</p>
            <p>Cảm ơn bạn đã đặt vé tại UniCine. Đặt vé của bạn đã được xác nhận thành công!</p>

            <div class="qr-section">
                <h3>MÃ QR ĐẶT VÉ</h3>
                <img src="{{ asset($booking->qr_code_url) }}" alt="QR Code">
                <div class="booking-code">{{ $booking->booking_code }}</div>
                <p style="color: #666; font-size: 14px;">Vui lòng xuất trình mã QR này tại quầy để nhận vé</p>
            </div>

            <div class="details">
                <h3>THÔNG TIN SUẤT CHIẾU</h3>
                <div class="detail-item">
                    <span class="detail-label">Phim:</span>
                    <span class="detail-value">{{ $movie->title }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Rạp:</span>
                    <span class="detail-value">{{ $cinema->name }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Phòng chiếu:</span>
                    <span class="detail-value">{{ $theater->name }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Thời gian:</span>
                    <span class="detail-value">{{ $showtime->start_time->format('H:i - d/m/Y') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Số lượng vé:</span>
                    <span class="detail-value">{{ $tickets->count() }} vé</span>
                </div>
            </div>

            <div class="seats-list">
                <strong>Ghế đã đặt:</strong><br>
                @foreach ($tickets as $ticket)
                    <span class="seat-item">{{ $ticket->seat->row }}{{ $ticket->seat->number }}</span>
                @endforeach
            </div>

            <div class="total-amount">
                TỔNG TIỀN: {{ number_format($booking->total_amount, 0, ',', '.') }}đ
            </div>

            <div class="important-note">
                <strong>⚠️ LƯU Ý QUAN TRỌNG:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Vui lòng có mặt trước giờ chiếu <strong>15 phút</strong></li>
                    <li>Mang theo <strong>mã QR</strong> này để soát vé tại quầy</li>
                    <li>Vé không được hoàn lại sau khi đã soát vé</li>
                    <li>Liên hệ hotline: <strong>1900 xxxx</strong> nếu cần hỗ trợ</li>
                </ul>
            </div>

            <p>Chúc bạn có trải nghiệm xem phim tuyệt vời! 🎬🍿</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} UniCine. All rights reserved.</p>
            <p>Email: support@UniCine.vn | Hotline: 1900 xxxx</p>
        </div>
    </div>
</body>

</html>
