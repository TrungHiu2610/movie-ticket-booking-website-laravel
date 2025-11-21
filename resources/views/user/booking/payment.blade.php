@extends('layouts.user')

@section('title', 'Thanh toán')

@section('content')
    <div class="payment-container">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="payment-card">
                        <div class="payment-header">
                            <i class="bi bi-credit-card"></i>
                            <h2>Thanh toán đặt vé</h2>
                            <p class="text-muted">Thanh toán qua VNPay - An toàn & Bảo mật</p>
                        </div>

                        <div class="booking-summary">
                            <h5><i class="bi bi-film me-2"></i>{{ $showtime->movie->title }}</h5>
                            <div class="summary-details">
                                <div class="detail-item">
                                    <i class="bi bi-geo-alt"></i>
                                    <span>{{ $showtime->theater->cinema->name }} - {{ $showtime->theater->name }}</span>
                                </div>
                                <div class="detail-item">
                                    <i class="bi bi-calendar"></i>
                                    <span>{{ \Carbon\Carbon::parse($showtime->start_time)->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>

                            <div class="seats-info">
                                <h6>Ghế đã chọn:</h6>
                                <div class="seat-badges">
                                    @foreach ($seatDetails as $seat)
                                        <span class="seat-badge">{{ $seat['name'] }}</span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="price-breakdown">
                                @foreach ($seatDetails as $seat)
                                    <div class="price-item">
                                        <span>{{ $seat['name'] }} ({{ $seat['type'] }})</span>
                                        <span>{{ number_format($seat['price']) }}đ</span>
                                    </div>
                                @endforeach

                                <div class="price-divider"></div>

                                @if (isset($loyaltyDiscount) && $loyaltyDiscount > 0)
                                    <div class="price-item discount-item">
                                        <span>Giảm giá thành viên</span>
                                        <span class="text-success">-{{ number_format($loyaltyDiscount) }}đ</span>
                                    </div>
                                @endif

                                <div class="price-item" id="voucher-discount-row" style="display: none;">
                                    <span>Giảm giá voucher</span>
                                    <span class="text-success" id="voucher-discount-amount">-0đ</span>
                                </div>

                                <div class="price-divider"></div>

                                <div class="price-total">
                                    <span>Tổng cộng</span>
                                    <span class="total-amount" id="final-total">{{ number_format($totalAmount) }}đ</span>
                                </div>
                            </div>

                            <div class="voucher-section">
                                <h6><i class="bi bi-tag me-2"></i>Mã giảm giá</h6>
                                <div class="voucher-input-group">
                                    <input type="text" id="voucher-code-input" class="form-control" />
                                    <button type="button" class="btn-apply-voucher" onclick="applyVoucher()">Áp
                                        dụng</button>
                                </div>
                                <div id="voucher-message" class="voucher-message"></div>
                                <div id="voucher-success" class="voucher-success" style="display: none;">
                                    <i class="bi bi-check-circle"></i>
                                    <span id="voucher-desc"></span>
                                    <button type="button" class="btn-remove-voucher" onclick="removeVoucher()">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="payment-actions">
                            <form action="{{ route('payment.create') }}" method="POST" id="paymentForm">
                                @csrf
                                <input type="hidden" name="voucher_code" id="voucher-code-hidden" value="" />
                                <button type="submit" class="btn-pay">
                                    <i class="bi bi-wallet2 me-2"></i>
                                    Thanh toán với VNPay
                                </button>
                            </form>

                            <a href="{{ route('booking.seats', $showtime->id) }}" class="btn-back">
                                <i class="bi bi-arrow-left me-2"></i>
                                Quay lại chọn ghế
                            </a>
                        </div>

                        <div class="reservation-timer">
                            <i class="bi bi-clock-history"></i>
                            <span>Thời gian giữ ghế còn lại: <strong id="timer">05:00</strong></span>
                        </div>

                        <div class="payment-note">
                            <i class="bi bi-info-circle"></i>
                            <div>
                                <strong>Lưu ý:</strong>
                                <ul>
                                    <li>Sau khi nhấn "Thanh toán với VNPay", bạn sẽ được chuyển đến cổng thanh toán VNPay
                                    </li>
                                    <li>Hỗ trợ thanh toán qua thẻ ATM, thẻ tín dụng, QR Code</li>
                                    <li>Ghế của bạn sẽ được giữ trong 5 phút</li>
                                    <li>Vé điện tử sẽ được gửi về email sau khi thanh toán thành công</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .payment-container {
            min-height: calc(100vh - 200px);
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            padding-top: 80px;
        }

        .payment-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        .payment-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .payment-header i {
            font-size: 60px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .payment-header h2 {
            color: #fff;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .booking-summary {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .booking-summary h5 {
            color: #fff;
            font-size: 22px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .summary-details {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.8);
        }

        .detail-item i {
            color: var(--primary-color);
            font-size: 18px;
        }

        .seats-info {
            margin: 20px 0;
        }

        .seats-info h6 {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 10px;
            font-size: 16px;
        }

        .seat-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .seat-badge {
            background: linear-gradient(135deg, var(--primary-color), #ff1744);
            color: #fff;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .price-breakdown {
            margin-top: 25px;
        }

        .price-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            color: rgba(255, 255, 255, 0.8);
            font-size: 15px;
        }

        .price-divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            margin: 15px 0;
        }

        .price-total {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            font-size: 20px;
            font-weight: 700;
            color: #fff;
        }

        .total-amount {
            color: var(--primary-color);
            font-size: 24px;
        }

        .payment-actions {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin: 30px 0;
        }

        .btn-pay {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #d60a5e, var(--primary-color));
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 20px rgba(229, 9, 20, 0.4);
        }

        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(229, 9, 20, 0.6);
        }

        .btn-back {
            width: 100%;
            padding: 14px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            color: #fff;
        }

        .reservation-timer {
            background: linear-gradient(135deg, rgba(255, 152, 0, 0.2), rgba(255, 87, 34, 0.2));
            border: 2px solid rgba(255, 152, 0, 0.5);
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            margin: 25px 0;
            color: #fff;
        }

        .reservation-timer i {
            font-size: 24px;
            color: #ff9800;
            margin-right: 10px;
        }

        .reservation-timer strong {
            color: #ff9800;
            font-size: 20px;
        }

        .payment-note {
            background: rgba(33, 150, 243, 0.1);
            border-left: 4px solid #2196f3;
            border-radius: 8px;
            padding: 20px;
            margin-top: 25px;
            display: flex;
            gap: 15px;
        }

        .payment-note i {
            font-size: 24px;
            color: #2196f3;
            margin-top: 5px;
        }

        .payment-note strong {
            color: #fff;
            display: block;
            margin-bottom: 8px;
        }

        .payment-note ul {
            margin: 0;
            padding-left: 20px;
            color: rgba(255, 255, 255, 0.8);
        }

        .payment-note li {
            margin-bottom: 5px;
        }

        .voucher-section {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .voucher-section h6 {
            color: #fff;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .voucher-input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .voucher-input-group input {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            border-radius: 10px;
            font-size: 15px;
        }

        .voucher-input-group input:focus {
            outline: none;
            border-color: #e50914;
            background: rgba(255, 255, 255, 0.08);
        }

        .btn-apply-voucher {
            padding: 12px 24px;
            background: linear-gradient(135deg, #e50914, #d60a5e);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-apply-voucher:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(229, 9, 20, 0.4);
        }

        .voucher-message {
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 14px;
            margin-top: 10px;
        }

        .voucher-message.error {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.5);
            color: #ff6b6b;
        }

        .voucher-success {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid rgba(40, 167, 69, 0.5);
            border-radius: 8px;
            margin-top: 10px;
            color: #4caf50;
        }

        .voucher-success i {
            font-size: 20px;
        }

        .voucher-success span {
            flex: 1;
            font-size: 14px;
        }

        .btn-remove-voucher {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-remove-voucher:hover {
            background: rgba(220, 53, 69, 0.8);
        }

        .discount-item {
            color: #4caf50;
        }

        @media (max-width: 768px) {
            .payment-card {
                padding: 25px;
            }

            .payment-header i {
                font-size: 50px;
            }

            .payment-header h2 {
                font-size: 24px;
            }

            .seat-badges {
                justify-content: center;
            }

            .price-total {
                font-size: 18px;
            }

            .total-amount {
                font-size: 22px;
            }
        }
    </style>

    <script>
        // Timer countdown
        let reservationExpiry = new Date(new Date().getTime() + 5 * 60000); // 5 minutes from now

        function updateTimer() {
            const now = new Date().getTime();
            const distance = reservationExpiry - now;

            if (distance < 0) {
                document.getElementById('timer').textContent = '00:00';
                alert('Thời gian giữ ghế đã hết! Vui lòng đặt lại.');
                window.location.href = '{{ route('movies.show', $showtime->movie->id) }}';
                return;
            }

            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('timer').textContent =
                String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        }

        // Update timer every second
        setInterval(updateTimer, 1000);
        updateTimer();

        let appliedVoucher = null;

        async function applyVoucher() {
            const voucherCode = document.getElementById('voucher-code-input').value.trim();
            const messageDiv = document.getElementById('voucher-message');

            if (!voucherCode) {
                messageDiv.className = 'voucher-message error';
                messageDiv.textContent = 'Vui lòng nhập mã giảm giá';
                return;
            }

            try {
                const response = await fetch('{{ route('payment.validate-voucher') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        voucher_code: voucherCode
                    })
                });

                const data = await response.json();

                if (data.valid) {
                    appliedVoucher = data.voucher;
                    document.getElementById('voucher-code-hidden').value = voucherCode;
                    document.getElementById('voucher-success').style.display = 'flex';
                    document.getElementById('voucher-desc').textContent = data.voucher.description;
                    document.getElementById('voucher-discount-row').style.display = 'flex';
                    document.getElementById('voucher-discount-amount').textContent =
                        '-' + data.voucher.discount_amount.toLocaleString('vi-VN') + 'đ';
                    document.getElementById('final-total').textContent =
                        data.pricing.final_total.toLocaleString('vi-VN') + 'đ';
                    document.getElementById('voucher-code-input').value = '';
                    messageDiv.textContent = '';
                    messageDiv.className = 'voucher-message';
                } else {
                    messageDiv.className = 'voucher-message error';
                    messageDiv.textContent = data.message;
                }
            } catch (error) {
                messageDiv.className = 'voucher-message error';
                messageDiv.textContent = 'Có lỗi xảy ra. Vui lòng thử lại.';
            }
        }

        function removeVoucher() {
            appliedVoucher = null;
            document.getElementById('voucher-code-hidden').value = '';
            document.getElementById('voucher-success').style.display = 'none';
            document.getElementById('voucher-discount-row').style.display = 'none';
            document.getElementById('final-total').textContent = '{{ number_format($totalAmount) }}đ';
            document.getElementById('voucher-message').textContent = '';
            document.getElementById('voucher-message').className = 'voucher-message';
        }


        // Prevent multiple form submissions
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
        });
    </script>
@endsection
