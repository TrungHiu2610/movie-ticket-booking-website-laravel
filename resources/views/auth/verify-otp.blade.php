@extends('layouts.app')

@section('title', 'Xác nhận OTP')

@section('content')
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="bi bi-envelope-check" style="font-size: 3rem; color: #0d6efd;"></i>
                            </div>
                            <h3 class="fw-bold">Xác nhận Email</h3>
                            <p class="text-muted">
                                Chúng tôi đã gửi mã OTP đến<br>
                                <strong>{{ session('registration_data.email') }}</strong>
                            </p>
                            <p class="text-muted small">Vui lòng kiểm tra hộp thư của bạn</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register.verify.submit') }}" id="verifyForm">
                            @csrf

                            <div class="mb-4">
                                <label for="otp" class="form-label text-center d-block">Nhập mã OTP (6 số)</label>
                                <div class="d-flex justify-content-center gap-2" id="otpInputs">
                                    <input type="text" class="form-control text-center otp-input" maxlength="1"
                                        style="width: 50px; height: 50px; font-size: 1.5rem; font-weight: bold;"
                                        data-index="0">
                                    <input type="text" class="form-control text-center otp-input" maxlength="1"
                                        style="width: 50px; height: 50px; font-size: 1.5rem; font-weight: bold;"
                                        data-index="1">
                                    <input type="text" class="form-control text-center otp-input" maxlength="1"
                                        style="width: 50px; height: 50px; font-size: 1.5rem; font-weight: bold;"
                                        data-index="2">
                                    <input type="text" class="form-control text-center otp-input" maxlength="1"
                                        style="width: 50px; height: 50px; font-size: 1.5rem; font-weight: bold;"
                                        data-index="3">
                                    <input type="text" class="form-control text-center otp-input" maxlength="1"
                                        style="width: 50px; height: 50px; font-size: 1.5rem; font-weight: bold;"
                                        data-index="4">
                                    <input type="text" class="form-control text-center otp-input" maxlength="1"
                                        style="width: 50px; height: 50px; font-size: 1.5rem; font-weight: bold;"
                                        data-index="5">
                                </div>
                                <input type="hidden" name="otp" id="otpValue">
                            </div>

                            <div class="text-center mb-3">
                                <div id="timer" class="text-warning mb-2" style="display: none;">
                                    <i class="bi bi-clock"></i> <span id="timeRemaining"></span>
                                </div>
                                <div id="resendSection">
                                    <p class="text-muted small mb-2">Chưa nhận được mã?</p>
                                    <button type="button" class="btn btn-link" id="resendBtn">
                                        <i class="bi bi-arrow-clockwise"></i> Gửi lại mã OTP
                                    </button>
                                    <div class="text-muted small mt-2" id="resendInfo">
                                        Bạn có thể gửi lại <span id="remainingResends">2</span> lần
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                                    <i class="bi bi-check-circle"></i> Xác nhận
                                </button>
                            </div>

                            <div class="text-center">
                                <a href="{{ route('register') }}" class="text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i> Quay lại đăng ký
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const otpInputs = document.querySelectorAll('.otp-input');
            const otpValue = document.getElementById('otpValue');
            const submitBtn = document.getElementById('submitBtn');
            const resendBtn = document.getElementById('resendBtn');
            const timer = document.getElementById('timer');
            const timeRemaining = document.getElementById('timeRemaining');
            const remainingResendsSpan = document.getElementById('remainingResends');
            const verifyForm = document.getElementById('verifyForm');

            let countdown = null;
            let remainingResends = 2;

            // Auto focus first input
            otpInputs[0].focus();

            // OTP input handling
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    // Only allow numbers
                    this.value = this.value.replace(/[^0-9]/g, '');

                    // Move to next input
                    if (this.value && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }

                    updateOTPValue();
                });

                input.addEventListener('keydown', function(e) {
                    // Handle backspace
                    if (e.key === 'Backspace' && !this.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }

                    // Handle paste
                    if (e.key === 'v' && (e.ctrlKey || e.metaKey)) {
                        e.preventDefault();
                        navigator.clipboard.readText().then(text => {
                            const numbers = text.replace(/[^0-9]/g, '').slice(0, 6);
                            numbers.split('').forEach((num, i) => {
                                if (otpInputs[i]) {
                                    otpInputs[i].value = num;
                                }
                            });
                            updateOTPValue();
                            if (numbers.length === 6) {
                                otpInputs[5].focus();
                            }
                        });
                    }
                });
            });

            function updateOTPValue() {
                const otp = Array.from(otpInputs).map(input => input.value).join('');
                otpValue.value = otp;
                submitBtn.disabled = otp.length !== 6;
            }

            // Start initial countdown
            @php
                $expiresAt = session('otp_expires_at');
                $secondsRemaining = $expiresAt ? max(0, $expiresAt - time()) : 600;
            @endphp
            startCountdown({{ $secondsRemaining }});

            function startCountdown(seconds) {
                if (countdown) clearInterval(countdown);

                let remaining = seconds;
                timer.style.display = 'block';
                resendBtn.disabled = true;
                resendBtn.classList.add('disabled');

                countdown = setInterval(function() {
                    remaining--;
                    const minutes = Math.floor(remaining / 60);
                    const secs = remaining % 60;
                    timeRemaining.textContent =
                        `Mã OTP hết hạn sau ${minutes}:${secs.toString().padStart(2, '0')}`;

                    if (remaining <= 0) {
                        clearInterval(countdown);
                        timer.style.display = 'none';
                        resendBtn.disabled = false;
                        resendBtn.classList.remove('disabled');

                        // Clear inputs
                        otpInputs.forEach(input => input.value = '');
                        updateOTPValue();

                        alert('Mã OTP đã hết hạn. Vui lòng gửi lại!');
                    }
                }, 1000);
            }

            // Resend OTP
            resendBtn.addEventListener('click', function() {
                if (remainingResends <= 0) {
                    alert('Bạn đã sử dụng hết số lần gửi lại. Vui lòng đăng ký lại!');
                    window.location.href = '{{ route('register') }}';
                    return;
                }

                resendBtn.disabled = true;
                resendBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang gửi...';

                fetch('{{ route('register.resend') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Clear inputs
                            otpInputs.forEach(input => input.value = '');
                            updateOTPValue();
                            otpInputs[0].focus();

                            // Update remaining resends
                            remainingResends = data.remaining_resends;
                            remainingResendsSpan.textContent = remainingResends;

                            // Start countdown
                            startCountdown(data.expires_in);

                            alert('Mã OTP mới đã được gửi đến email của bạn!');

                            if (remainingResends === 0) {
                                document.getElementById('resendInfo').innerHTML =
                                    '<span class="text-danger">Đây là lần gửi cuối cùng!</span>';
                            }
                        } else {
                            alert(data.message || 'Có lỗi xảy ra khi gửi lại OTP!');
                            if (data.message && data.message.includes('đăng ký lại')) {
                                window.location.href = '{{ route('register') }}';
                            }
                        }

                        resendBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Gửi lại mã OTP';
                        resendBtn.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra. Vui lòng thử lại!');
                        resendBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Gửi lại mã OTP';
                        resendBtn.disabled = false;
                    });
            });

            // Form submit
            verifyForm.addEventListener('submit', function(e) {
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm"></span> Đang xác nhận...';
            });
        });
    </script>
@endpush
