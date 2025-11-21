<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phiếu hoàn tiền - {{ $refund->refund_code }}</title>
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            color: #E50914;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .info-section h3 {
            background: #f5f5f5;
            padding: 10px;
            border-left: 4px solid #E50914;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table td,
        table th {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        table th {
            text-align: left;
            width: 200px;
            color: #666;
        }

        .total-box {
            background: #f9f9f9;
            border: 2px solid #E50914;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }

        .total-box .amount {
            font-size: 32px;
            font-weight: bold;
            color: #E50914;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            width: 45%;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }

        .print-btn {
            background: #E50914;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin: 20px auto;
            display: block;
        }

        .print-btn:hover {
            background: #d00812;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>UniCine</h1>
        <p style="margin: 5px 0;">Phiếu xác nhận hoàn tiền</p>
        <p style="margin: 0; font-size: 14px; color: #666;">
            Mã giao dịch: <strong>{{ $refund->refund_code }}</strong>
        </p>
    </div>

    <div class="info-section">
        <h3>Thông tin khách hàng</h3>
        <table>
            <tr>
                <th>Họ và tên:</th>
                <td>{{ $refund->booking->user->name }}</td>
            </tr>
            <tr>
                <th>Email:</th>
                <td>{{ $refund->booking->user->email }}</td>
            </tr>
            <tr>
                <th>Số điện thoại:</th>
                <td>{{ $refund->booking->user->phone ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="info-section">
        <h3>Thông tin vé</h3>
        <table>
            <tr>
                <th>Mã đặt vé:</th>
                <td><strong>{{ $refund->booking->booking_code }}</strong></td>
            </tr>
            <tr>
                <th>Phim:</th>
                <td>{{ $refund->booking->showtime->movie->title }}</td>
            </tr>
            <tr>
                <th>Suất chiếu:</th>
                <td>{{ $refund->booking->showtime->start_time->format('H:i - d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Rạp:</th>
                <td>{{ $refund->booking->showtime->theater->cinema->name }}</td>
            </tr>
            <tr>
                <th>Phòng chiếu:</th>
                <td>{{ $refund->booking->showtime->theater->name }}</td>
            </tr>
            <tr>
                <th>Ghế đã đặt:</th>
                <td>
                    @foreach ($refund->booking->tickets as $ticket)
                        <span style="background: #f5f5f5; padding: 3px 8px; margin-right: 5px; border-radius: 3px;">
                            {{ $ticket->seat->row_char }}{{ $ticket->seat->column_number }}
                        </span>
                    @endforeach
                </td>
            </tr>
        </table>
    </div>

    <div class="info-section">
        <h3>Chi tiết hoàn tiền</h3>
        <table>
            <tr>
                <th>Số tiền gốc:</th>
                <td>{{ number_format($refund->original_amount, 0, ',', '.') }}đ</td>
            </tr>
            <tr>
                <th>Phí hoàn tiền:</th>
                <td style="color: #E50914;">
                    -{{ number_format($refund->refund_fee, 0, ',', '.') }}đ
                    @if ($refund->refund_fee > 0)
                        ({{ round(($refund->refund_fee / $refund->original_amount) * 100, 1) }}%)
                    @endif
                </td>
            </tr>
            @if ($refund->reason)
                <tr>
                    <th>Lý do hoàn tiền:</th>
                    <td>{{ $refund->reason }}</td>
                </tr>
            @endif
            <tr>
                <th>Thời gian xử lý:</th>
                <td>{{ $refund->processed_at ? $refund->processed_at->format('H:i - d/m/Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Nhân viên xử lý:</th>
                <td>{{ $refund->processedBy ? $refund->processedBy->name : 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="total-box">
        <p style="margin: 0; font-size: 18px;">Tổng số tiền hoàn lại</p>
        <div class="amount">{{ number_format($refund->refund_amount, 0, ',', '.') }}đ</div>
        <p style="margin: 10px 0 0 0; font-size: 14px; color: #666;">
            (Bằng chữ: {{ ucfirst(number_to_vietnamese_words($refund->refund_amount)) }} đồng)
        </p>
    </div>

    @if ($refund->staff_notes)
        <div class="info-section">
            <h3>Ghi chú</h3>
            <p style="padding: 15px; background: #f9f9f9; border-left: 4px solid #ffc107;">
                {{ $refund->staff_notes }}
            </p>
        </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <p><strong>Người nhận tiền</strong></p>
            <p style="font-size: 14px; color: #666;">(Ký và ghi rõ họ tên)</p>
            <div class="signature-line"></div>
        </div>
        <div class="signature-box">
            <p><strong>Nhân viên thu ngân</strong></p>
            <p style="font-size: 14px; color: #666;">(Ký và ghi rõ họ tên)</p>
            <div class="signature-line">
                {{ $refund->processedBy ? $refund->processedBy->name : '' }}
            </div>
        </div>
    </div>

    <div class="footer">
        <p><strong>UniCine - Hệ thống rạp chiếu phim</strong></p>
        <p>Hotline: 1900 xxxx | Email: support@UniCine.vn</p>
        <p style="font-size: 12px; margin-top: 10px;">
            Phiếu này có giá trị làm chứng từ hoàn tiền. Vui lòng giữ lại để đối chiếu.
        </p>
    </div>

    <script></script>
</body>

</html>

<?php
function number_to_vietnamese_words($number)
{
    $number = (int) $number;
    if ($number == 0) {
        return 'không';
    }

    $units = ['', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
    $levels = ['', 'nghìn', 'triệu', 'tỷ'];

    $result = '';
    $level = 0;

    while ($number > 0) {
        $temp = $number % 1000;
        if ($temp != 0) {
            $hundred = floor($temp / 100);
            $ten = floor(($temp % 100) / 10);
            $unit = $temp % 10;

            $tempResult = '';
            if ($hundred > 0) {
                $tempResult .= $units[$hundred] . ' trăm ';
            }
            if ($ten > 1) {
                $tempResult .= $units[$ten] . ' mươi ';
            } elseif ($ten == 1) {
                $tempResult .= 'mười ';
            }
            if ($unit > 0) {
                $tempResult .= $units[$unit];
            }

            $result = $tempResult . ' ' . $levels[$level] . ' ' . $result;
        }
        $number = floor($number / 1000);
        $level++;
    }

    return trim($result);
}
?>
