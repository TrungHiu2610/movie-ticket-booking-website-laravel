<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Nhắc nhở suất chiếu</title>
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
            background: #ffa500;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 30px;
        }

        .time-box {
            background: #fff3cd;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border-left: 4px solid #ffa500;
        }

        .time {
            font-size: 28px;
            font-weight: bold;
            color: #ff8c00;
            margin: 10px 0;
        }

        table {
            width: 100%;
            margin: 20px 0;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .label {
            font-weight: bold;
            width: 100px;
        }

        .code-box {
            background: #e50914;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
        }

        .note {
            background: #fff3cd;
            padding: 15px;
            margin: 20px 0;
            border-left: 3px solid #ffc107;
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
            <h2>Nhắc nhở suất chiếu</h2>
            <p>UniCine</p>
        </div>
        <div class="content">
            <p>Xin chào <strong>{{ $user->name }}</strong>,</p>
            <div class="time-box">
                <p>Suất chiếu sắp bắt đầu</p>
                <div class="time">{{ $showtime->start_time->format('H:i') }}</div>
                <p>{{ $showtime->start_time->format('d/m/Y') }}</p>
            </div>
            <table>
                <tr>
                    <td class="label">Phim:</td>
                    <td>{{ $movie->title }}</td>
                </tr>
                <tr>
                    <td class="label">Rạp:</td>
                    <td>{{ $showtime->theater->cinema->name }}</td>
                </tr>
                <tr>
                    <td class="label">Phòng:</td>
                    <td>{{ $showtime->theater->name }}</td>
                </tr>
                <tr>
                    <td class="label">Địa chỉ:</td>
                    <td>{{ $showtime->theater->cinema->address }}</td>
                </tr>
            </table>
            <div class="code-box">
                {{ $booking->booking_code }}
            </div>
            <div class="note">
                <strong>Lưu ý:</strong>
                <ul>
                    <li>Có mặt trước 15-30 phút</li>
                    <li>Mang mã QR để soát vé</li>
                    <li>Chuẩn bị CMND nếu phim có giới hạn tuổi</li>
                </ul>
            </div>
            <p>Chúc xem phim vui vẻ!</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} UniCine | support@unicine.vn | 1900 xxxx</p>
        </div>
    </div>
</body>

</html>
