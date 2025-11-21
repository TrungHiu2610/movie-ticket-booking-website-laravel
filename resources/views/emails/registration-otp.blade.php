<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Mã OTP đăng ký</title>
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
            background: #e50914;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 30px;
        }

        .otp-box {
            background: #f5f5f5;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border: 2px dashed #e50914;
        }

        .otp-code {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #e50914;
            margin: 10px 0;
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
            <h2>UniCine</h2>
            <p>Xác thực đăng ký tài khoản</p>
        </div>
        <div class="content">
            <p>Xin chào <strong>{{ $name }}</strong>,</p>
            <p>Cảm ơn bạn đã đăng ký. Vui lòng nhập mã OTP bên dưới để hoàn tất:</p>
            <div class="otp-box">
                <p>MÃ OTP</p>
                <div class="otp-code">{{ $otp }}</div>
                <p>Hiệu lực: {{ $expires_minutes }} phút</p>
            </div>
            <div class="note">
                <strong>Lưu ý:</strong>
                <ul>
                    <li>Mã có hiệu lực {{ $expires_minutes }} phút</li>
                    <li>Không chia sẻ mã với bất kỳ ai</li>
                    <li>Nếu không đăng ký, bỏ qua email này</li>
                </ul>
            </div>
            <p>Hỗ trợ: support@unicine.vn | 1900 xxxx</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} UniCine</p>
        </div>
    </div>
</body>

</html>
