@extends('layouts.app')

@section('title', 'Quên mật khẩu')

@section('content')
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold">Quên mật khẩu?</h3>
                            <p class="text-muted">Nhập email để nhận mã OTP xác nhận</p>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form id="forgotPasswordForm">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Địa chỉ Email</label>
                                <div class="input-group">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}"
                                        placeholder="example@email.com" required autofocus>
                                    <button type="button" class="btn btn-primary" id="sendOtpBtn" disabled>
                                        <i class="bi bi-send"></i> Gửi OTP
                                    </button>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- OTP Field (hidden initially) -->
                            <div class="mb-3" id="otpField" style="display: none;">
                                <label for="otp" class="form-label">Mã OTP</label>
                                <input type="text" class="form-control" id="otp" name="otp"
                                    placeholder="Nhập 6 số OTP" maxlength="6" pattern="\d{6}">
                                <div class="form-text text-success" id="otpSentMsg" style="display: none;">
                                    <i class="bi bi-check-circle"></i> Mã OTP đã được gửi đến email của bạn
                                </div>
                                <div class="form-text text-warning" id="otpTimer" style="display: none;"></div>
                            </div>

                            <!-- New Password Fields (hidden initially) -->
                            <div id="passwordFields" style="display: none;">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu mới</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Nhập mật khẩu mới" minlength="8">
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" placeholder="Nhập lại mật khẩu mới" minlength="8">
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="button" class="btn btn-success" id="verifyOtpBtn" style="display: none;">
                                    <i class="bi bi-check-circle"></i> Xác nhận OTP
                                </button>
                                <button type="submit" class="btn btn-primary" id="resetPasswordBtn" style="display: none;">
                                    <i class="bi bi-key"></i> Đặt lại mật khẩu
                                </button>
                            </div>

                            <div class="text-center">
                                <a href="{{ route('login') }}" class="text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i> Quay lại đăng nhập
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
            const emailInput = document.getElementById('email');
            const sendOtpBtn = document.getElementById('sendOtpBtn');
            const otpField = document.getElementById('otpField');
            const otpInput = document.getElementById('otp');
            const otpSentMsg = document.getElementById('otpSentMsg');
            const otpTimer = document.getElementById('otpTimer');
            const verifyOtpBtn = document.getElementById('verifyOtpBtn');
            const passwordFields = document.getElementById('passwordFields');
            const resetPasswordBtn = document.getElementById('resetPasswordBtn');
            const forgotPasswordForm = document.getElementById('forgotPasswordForm');

            let otpCountdown = null;
            let verificationToken = null;

            // Enable OTP button when email is valid
            emailInput.addEventListener('input', function() {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                sendOtpBtn.disabled = !emailPattern.test(this.value);
            });

            // Send OTP
            sendOtpBtn.addEventListener('click', function() {
                const email = emailInput.value;

                if (!email) {
                    alert('Vui lòng nhập email!');
                    return;
                }

                sendOtpBtn.disabled = true;
                sendOtpBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang gửi...';

                fetch('/api/otp/send', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            email: email,
                            type: 'password_reset'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            otpField.style.display = 'block';
                            otpSentMsg.style.display = 'block';
                            verifyOtpBtn.style.display = 'block';
                            otpInput.focus();

                            startOtpCountdown(data.expires_in);

                            emailInput.readOnly = true;
                            sendOtpBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Gửi lại';
                        } else {
                            alert(data.message || 'Có lỗi xảy ra khi gửi OTP!');
                            sendOtpBtn.disabled = false;
                            sendOtpBtn.innerHTML = '<i class="bi bi-send"></i> Gửi OTP';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra. Vui lòng thử lại!');
                        sendOtpBtn.disabled = false;
                        sendOtpBtn.innerHTML = '<i class="bi bi-send"></i> Gửi OTP';
                    });
            });

            // Verify OTP
            verifyOtpBtn.addEventListener('click', function() {
                const email = emailInput.value;
                const otp = otpInput.value;

                if (!otp || otp.length !== 6) {
                    alert('Vui lòng nhập đầy đủ 6 số OTP!');
                    return;
                }

                verifyOtpBtn.disabled = true;
                verifyOtpBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm"></span> Đang xác nhận...';

                fetch('/api/otp/verify', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            email: email,
                            otp: otp,
                            type: 'password_reset'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Store verification token
                            verificationToken = data.verification_token;

                            // Hide OTP field and verify button
                            otpField.style.display = 'none';
                            verifyOtpBtn.style.display = 'none';
                            otpSentMsg.style.display = 'none';

                            // Show password fields
                            passwordFields.style.display = 'block';
                            resetPasswordBtn.style.display = 'block';

                            // Clear countdown
                            if (otpCountdown) clearInterval(otpCountdown);
                            otpTimer.style.display = 'none';

                            alert('OTP xác nhận thành công! Vui lòng nhập mật khẩu mới.');
                        } else {
                            alert(data.message || 'Mã OTP không đúng!');
                            verifyOtpBtn.disabled = false;
                            verifyOtpBtn.innerHTML = '<i class="bi bi-check-circle"></i> Xác nhận OTP';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra. Vui lòng thử lại!');
                        verifyOtpBtn.disabled = false;
                        verifyOtpBtn.innerHTML = '<i class="bi bi-check-circle"></i> Xác nhận OTP';
                    });
            });

            // Reset Password
            forgotPasswordForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const password = document.getElementById('password').value;
                const passwordConfirmation = document.getElementById('password_confirmation').value;

                if (!password || !passwordConfirmation) {
                    alert('Vui lòng nhập đầy đủ mật khẩu!');
                    return;
                }

                if (password !== passwordConfirmation) {
                    alert('Mật khẩu xác nhận không khớp!');
                    return;
                }

                if (password.length < 8) {
                    alert('Mật khẩu phải có ít nhất 8 ký tự!');
                    return;
                }

                resetPasswordBtn.disabled = true;
                resetPasswordBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm"></span> Đang xử lý...';

                fetch('/api/otp/reset-password', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            verification_token: verificationToken,
                            password: password,
                            password_confirmation: passwordConfirmation
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Đặt lại mật khẩu thành công! Đang chuyển đến trang đăng nhập...');
                            window.location.href = '/login';
                        } else {
                            alert(data.message || 'Có lỗi xảy ra!');
                            resetPasswordBtn.disabled = false;
                            resetPasswordBtn.innerHTML = '<i class="bi bi-key"></i> Đặt lại mật khẩu';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra. Vui lòng thử lại!');
                        resetPasswordBtn.disabled = false;
                        resetPasswordBtn.innerHTML = '<i class="bi bi-key"></i> Đặt lại mật khẩu';
                    });
            });

            function startOtpCountdown(seconds) {
                if (otpCountdown) clearInterval(otpCountdown);

                let remaining = seconds;
                otpTimer.style.display = 'block';
                sendOtpBtn.disabled = true;

                otpCountdown = setInterval(function() {
                    remaining--;
                    const minutes = Math.floor(remaining / 60);
                    const secs = remaining % 60;
                    otpTimer.innerHTML =
                        `<i class="bi bi-clock"></i> Có thể gửi lại sau ${minutes}:${secs.toString().padStart(2, '0')}`;

                    if (remaining <= 0) {
                        clearInterval(otpCountdown);
                        otpTimer.style.display = 'none';
                        sendOtpBtn.disabled = false;
                    }
                }, 1000);
            }
        });
    </script>
@endpush
