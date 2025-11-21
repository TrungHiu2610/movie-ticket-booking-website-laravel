<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Hủy đặt vé</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border: 1px solid #ddd;
        }

        .header {
            background: #666;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 30px;
        }

        .info-box {
            background: #f5f5f5;
            padding: 15px;
            margin: 20px 0;
        }

        .footer {
            background: #f9f9f9;
            padding: 15px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Thông báo hủy vé</h2>
        </div>
        <div class="content">
            <p>Xin chào <strong>{{ $user->name }}</strong>,</p>
            <p>Đặt vé của bạn đã được hủy.</p>
            <div class="info-box">
                <p><strong>Mã đặt vé:</strong> {{ $booking->booking_code }}</p>
                <p><strong>Phim:</strong> {{ $movie->title }}</p>
                <p><strong>Số tiền hoàn:</strong> {{ number_format($booking->total_amount, 0, ',', '.') }}đ</p>
            </div>
            <p>Số tiền sẽ được hoàn lại trong 3-5 ngày làm việc.</p>
            <p>Cảm ơn bạn!</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} UniCine</p>
        </div>
    </div>
</body>

</html>
