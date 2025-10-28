@extends('layouts.app')

@section('title', 'Xác thực Email')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="bi bi-envelope-check text-primary" style="font-size: 4rem;"></i>
                    </div>

                    <h3 class="fw-bold mb-3">Xác thực địa chỉ Email</h3>

                    <p class="text-muted mb-4">
                        Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, vui lòng xác thực địa chỉ email của bạn
                        bằng cách nhấp vào liên kết chúng tôi vừa gửi cho bạn qua email.
                        Nếu bạn không nhận được email, chúng tôi sẵn sàng gửi lại cho bạn.
                    </p>

                    @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success" role="alert">
                        Một liên kết xác thực mới đã được gửi đến địa chỉ email bạn đã đăng ký.
                    </div>
                    @endif

                    <div class="d-flex gap-2 justify-content-center">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-arrow-clockwise me-2"></i>Gửi lại email xác thực
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection