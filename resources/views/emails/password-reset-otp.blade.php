<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Đặt lại mật khẩu</title>
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

        .warning {
            background: #f8d7da;
            padding: 15px;
            margin: 20px 0;
            border-left: 3px solid #dc3545;
            color: #721c24;
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
            <p>Đặt lại mật khẩu</p>
        </div>
        <div class="content">
            <p>Xin chào <strong>{{ $name }}</strong>,</p>
            <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu. Vui lòng sử dụng mã OTP bên dưới:</p>
            <div class="otp-box">
                <p>MÃ OTP</p>
                <div class="otp-code">{{ $otp }}</div>
                <p>Hiệu lực: {{ $expires_minutes }} phút</p>
            </div>
            <div class="warning">
                <strong>Bảo mật:</strong>
                <ul>
                    <li>Mã có hiệu lực {{ $expires_minutes }} phút</li>
                    <li>Không chia sẻ với bất kỳ ai</li>
                    <li>Nếu không yêu cầu, bỏ qua email và đổi mật khẩu ngay</li>
                    <li>Liên hệ nếu nghi ngờ bị xâm nhập</li>
                </ul>
            </div>
            <p>Sau khi đổi mật khẩu, các phiên đăng nhập cũ sẽ bị đăng xuất.</p>
            <p>Hỗ trợ: support@unicine.vn | 1900 xxxx</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} UniCine</p>
        </div>
    </div>
</body>

</html>
