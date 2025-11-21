@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h3><i class="bi bi-person-plus"></i> Đăng ký tài khoản</h3>
                            <p class="text-muted">Tạo tài khoản mới</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Họ tên</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                <!-- Password Strength Indicator -->
                                <div class="mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" id="passwordStrengthBar" role="progressbar"
                                            style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted" id="passwordStrengthText">Nhập mật khẩu</small>
                                </div>

                                <!-- Password Requirements -->
                                <div class="mt-2">
                                    <small class="text-muted d-block mb-1">Mật khẩu phải có:</small>
                                    <ul class="list-unstyled mb-0" style="font-size: 0.85rem;">
                                        <li id="length-check" class="text-muted">
                                            <i class="bi bi-circle"></i> Ít nhất 8 ký tự
                                        </li>
                                        <li id="uppercase-check" class="text-muted">
                                            <i class="bi bi-circle"></i> Ít nhất 1 chữ hoa (A-Z)
                                        </li>
                                        <li id="lowercase-check" class="text-muted">
                                            <i class="bi bi-circle"></i> Ít nhất 1 chữ thường (a-z)
                                        </li>
                                        <li id="number-check" class="text-muted">
                                            <i class="bi bi-circle"></i> Ít nhất 1 số (0-9)
                                        </li>
                                        <li id="special-check" class="text-muted">
                                            <i class="bi bi-circle"></i> Ít nhất 1 ký tự đặc biệt (!@#$...)
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                        <i class="bi bi-eye" id="toggleIconConfirm"></i>
                                    </button>
                                </div>
                                <small class="text-muted" id="passwordMatchText"></small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-person-check"></i> Đăng ký
                                </button>
                            </div>

                            <hr class="my-4">

                            <div class="text-center">
                                <span class="text-muted">Đã có tài khoản?</span>
                                <a href="{{ route('login') }}" class="text-decoration-none fw-bold">
                                    Đăng nhập ngay
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const passwordInput = document.getElementById('password');
                const confirmPasswordInput = document.getElementById('password_confirmation');
                const strengthBar = document.getElementById('passwordStrengthBar');
                const strengthText = document.getElementById('passwordStrengthText');
                const matchText = document.getElementById('passwordMatchText');

                // Toggle password visibility
                document.getElementById('togglePassword').addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    const icon = document.getElementById('toggleIcon');
                    icon.classList.toggle('bi-eye');
                    icon.classList.toggle('bi-eye-slash');
                });

                document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
                    const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    confirmPasswordInput.setAttribute('type', type);
                    const icon = document.getElementById('toggleIconConfirm');
                    icon.classList.toggle('bi-eye');
                    icon.classList.toggle('bi-eye-slash');
                });

                // Password strength checker
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;

                    // Check length
                    const lengthCheck = document.getElementById('length-check');
                    if (password.length >= 8) {
                        strength += 20;
                        lengthCheck.innerHTML =
                            '<i class="bi bi-check-circle-fill text-success"></i> Ít nhất 8 ký tự';
                        lengthCheck.classList.remove('text-muted');
                        lengthCheck.classList.add('text-success');
                    } else {
                        lengthCheck.innerHTML = '<i class="bi bi-circle"></i> Ít nhất 8 ký tự';
                        lengthCheck.classList.remove('text-success');
                        lengthCheck.classList.add('text-muted');
                    }

                    // Check uppercase
                    const uppercaseCheck = document.getElementById('uppercase-check');
                    if (/[A-Z]/.test(password)) {
                        strength += 20;
                        uppercaseCheck.innerHTML =
                            '<i class="bi bi-check-circle-fill text-success"></i> Ít nhất 1 chữ hoa (A-Z)';
                        uppercaseCheck.classList.remove('text-muted');
                        uppercaseCheck.classList.add('text-success');
                    } else {
                        uppercaseCheck.innerHTML = '<i class="bi bi-circle"></i> Ít nhất 1 chữ hoa (A-Z)';
                        uppercaseCheck.classList.remove('text-success');
                        uppercaseCheck.classList.add('text-muted');
                    }

                    // Check lowercase
                    const lowercaseCheck = document.getElementById('lowercase-check');
                    if (/[a-z]/.test(password)) {
                        strength += 20;
                        lowercaseCheck.innerHTML =
                            '<i class="bi bi-check-circle-fill text-success"></i> Ít nhất 1 chữ thường (a-z)';
                        lowercaseCheck.classList.remove('text-muted');
                        lowercaseCheck.classList.add('text-success');
                    } else {
                        lowercaseCheck.innerHTML = '<i class="bi bi-circle"></i> Ít nhất 1 chữ thường (a-z)';
                        lowercaseCheck.classList.remove('text-success');
                        lowercaseCheck.classList.add('text-muted');
                    }

                    // Check number
                    const numberCheck = document.getElementById('number-check');
                    if (/[0-9]/.test(password)) {
                        strength += 20;
                        numberCheck.innerHTML =
                            '<i class="bi bi-check-circle-fill text-success"></i> Ít nhất 1 số (0-9)';
                        numberCheck.classList.remove('text-muted');
                        numberCheck.classList.add('text-success');
                    } else {
                        numberCheck.innerHTML = '<i class="bi bi-circle"></i> Ít nhất 1 số (0-9)';
                        numberCheck.classList.remove('text-success');
                        numberCheck.classList.add('text-muted');
                    }

                    // Check special character
                    const specialCheck = document.getElementById('special-check');
                    if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
                        strength += 20;
                        specialCheck.innerHTML =
                            '<i class="bi bi-check-circle-fill text-success"></i> Ít nhất 1 ký tự đặc biệt (!@#$...)';
                        specialCheck.classList.remove('text-muted');
                        specialCheck.classList.add('text-success');
                    } else {
                        specialCheck.innerHTML =
                            '<i class="bi bi-circle"></i> Ít nhất 1 ký tự đặc biệt (!@#$...)';
                        specialCheck.classList.remove('text-success');
                        specialCheck.classList.add('text-muted');
                    }

                    // Update strength bar
                    strengthBar.style.width = strength + '%';
                    strengthBar.classList.remove('bg-danger', 'bg-warning', 'bg-success');

                    if (strength === 0) {
                        strengthText.textContent = 'Nhập mật khẩu';
                        strengthText.classList.remove('text-danger', 'text-warning', 'text-success');
                        strengthText.classList.add('text-muted');
                    } else if (strength < 40) {
                        strengthBar.classList.add('bg-danger');
                        strengthText.textContent = 'Mật khẩu yếu';
                        strengthText.classList.remove('text-muted', 'text-warning', 'text-success');
                        strengthText.classList.add('text-danger');
                    } else if (strength < 80) {
                        strengthBar.classList.add('bg-warning');
                        strengthText.textContent = 'Mật khẩu trung bình';
                        strengthText.classList.remove('text-muted', 'text-danger', 'text-success');
                        strengthText.classList.add('text-warning');
                    } else {
                        strengthBar.classList.add('bg-success');
                        strengthText.textContent = 'Mật khẩu mạnh';
                        strengthText.classList.remove('text-muted', 'text-danger', 'text-warning');
                        strengthText.classList.add('text-success');
                    }

                    // Check password match
                    checkPasswordMatch();
                });

                // Check password confirmation match
                confirmPasswordInput.addEventListener('input', checkPasswordMatch);

                function checkPasswordMatch() {
                    const password = passwordInput.value;
                    const confirmPassword = confirmPasswordInput.value;

                    if (confirmPassword.length === 0) {
                        matchText.textContent = '';
                        confirmPasswordInput.classList.remove('is-valid', 'is-invalid');
                        return;
                    }

                    if (password === confirmPassword) {
                        matchText.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i> Mật khẩu khớp';
                        matchText.classList.remove('text-danger');
                        matchText.classList.add('text-success');
                        confirmPasswordInput.classList.remove('is-invalid');
                        confirmPasswordInput.classList.add('is-valid');
                    } else {
                        matchText.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i> Mật khẩu không khớp';
                        matchText.classList.remove('text-success');
                        matchText.classList.add('text-danger');
                        confirmPasswordInput.classList.remove('is-valid');
                        confirmPasswordInput.classList.add('is-invalid');
                    }
                }
            });
        </script>
    @endpush
@endsection
