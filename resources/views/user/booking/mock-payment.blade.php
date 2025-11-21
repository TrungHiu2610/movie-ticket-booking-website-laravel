@extends('layouts.user')

@section('title', 'Thanh toán giả lập')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-header bg-warning text-dark text-center">
                        <h4><i class="bi bi-credit-card"></i> MOCK PAYMENT - GIẢI LẬP THANH TOÁN</h4>
                        <p class="mb-0 small">Chế độ test - Không thanh toán thật</p>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Chế độ giả lập:</strong> Bạn đang ở chế độ test. Không có tiền thật được charge.
                        </div>

                        <div class="payment-info mb-4">
                            <h5 class="mb-3">Thông tin thanh toán:</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Mã giao dịch:</strong></td>
                                    <td class="text-end"><code>{{ $orderId }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Số tiền:</strong></td>
                                    <td class="text-end text-danger fs-4">
                                        <strong>{{ number_format($amount) }}đ</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Suất chiếu:</strong></td>
                                    <td class="text-end">#{{ $paymentData['showtime_id'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Số ghế:</strong></td>
                                    <td class="text-end">{{ count($paymentData['seat_ids']) }} ghế</td>
                                </tr>
                            </table>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Lưu ý:</strong> Bạn có 5 phút để hoàn tất thanh toán, sau đó ghế sẽ được giải phóng.
                        </div>

                        <div class="d-grid gap-3">
                            <form action="{{ route('payment.callback') }}" method="GET">
                                <input type="hidden" name="orderId" value="{{ $orderId }}">
                                <input type="hidden" name="resultCode" value="0">
                                <input type="hidden" name="message" value="Success">
                                <input type="hidden" name="amount" value="{{ $amount }}">
                                <input type="hidden" name="orderInfo" value="Mock payment">
                                <input type="hidden" name="orderType" value="momo_wallet">
                                <input type="hidden" name="transId" value="{{ time() }}">
                                <input type="hidden" name="payType" value="qr">
                                <input type="hidden" name="extraData"
                                    value="{{ base64_encode(json_encode(['user_id' => auth()->id(), 'showtime_id' => $paymentData['showtime_id'], 'seat_ids' => $paymentData['seat_ids']])) }}">
                                <input type="hidden" name="signature" value="mock_signature">
                                <input type="hidden" name="requestId" value="{{ $orderId }}">
                                <input type="hidden" name="responseTime" value="{{ time() }}">

                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="bi bi-check-circle"></i> Giả lập thanh toán THÀNH CÔNG
                                </button>
                            </form>

                            <form action="{{ route('payment.callback') }}" method="GET">
                                <input type="hidden" name="orderId" value="{{ $orderId }}">
                                <input type="hidden" name="resultCode" value="1006">
                                <input type="hidden" name="message" value="User cancelled">
                                <input type="hidden" name="amount" value="{{ $amount }}">

                                <button type="submit" class="btn btn-danger btn-lg w-100">
                                    <i class="bi bi-x-circle"></i> Giả lập thanh toán THẤT BẠI
                                </button>
                            </form>

                            <a href="{{ route('booking.seats', $paymentData['showtime_id']) }}"
                                class="btn btn-secondary btn-lg w-100">
                                <i class="bi bi-arrow-left"></i> Quay lại chọn ghế
                            </a>
                        </div>

                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">
                                <i class="bi bi-gear"></i> Hướng dẫn dành cho Dev:
                            </h6>
                            <ul class="small text-muted mb-0">
                                <li>Nút xanh: Test flow thanh toán thành công</li>
                                <li>Nút đỏ: Test flow thanh toán thất bại</li>
                                <li>Để bật MoMo thật: Sửa <code>$useMockPayment = false</code> trong
                                    <code>PaymentController.php</code></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            border: none;
            border-radius: 15px;
        }

        .card-header {
            border-radius: 15px 15px 0 0;
            padding: 1.5rem;
        }

        .payment-info {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
        }

        .btn-lg {
            padding: 1rem;
            font-size: 1.1rem;
            border-radius: 10px;
            font-weight: 600;
        }

        code {
            background: #e9ecef;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
        }
    </style>
@endsection
