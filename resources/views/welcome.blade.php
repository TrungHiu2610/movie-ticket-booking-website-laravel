@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="row py-5 my-5">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-3 fw-bold mb-4">
                <i class="bi bi-film text-primary"></i>
                Movie Ticket Booking
            </h1>
            <p class="lead text-muted mb-5">
                Đặt vé xem phim online nhanh chóng, tiện lợi.
                Trải nghiệm rạp chiếu phim hiện đại với hệ thống đặt vé thông minh.
            </p>

            @guest
            <div class="d-flex gap-3 justify-content-center">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-5">
                    <i class="bi bi-person-plus"></i> Đăng ký
                </a>
            </div>
            @else
            <div class="d-flex gap-3 justify-content-center">
                @if(auth()->user()->role->name === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-speedometer2"></i> Admin Dashboard
                </a>
                @else
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-ticket-perforated"></i> Đặt vé ngay
                </a>
                @endif
            </div>
            @endguest
        </div>
    </div>

    <!-- Features -->
    <div class="row g-4 py-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="fs-1 text-primary mb-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h5 class="card-title">Đặt vé nhanh chóng</h5>
                    <p class="card-text text-muted">
                        Chỉ với vài thao tác đơn giản, bạn có thể đặt vé xem phim yêu thích của mình.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="fs-1 text-success mb-3">
                        <i class="bi bi-credit-card"></i>
                    </div>
                    <h5 class="card-title">Thanh toán an toàn</h5>
                    <p class="card-text text-muted">
                        Hỗ trợ nhiều phương thức thanh toán với bảo mật cao nhất.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="fs-1 text-info mb-3">
                        <i class="bi bi-ticket-detailed"></i>
                    </div>
                    <h5 class="card-title">Vé điện tử</h5>
                    <p class="card-text text-muted">
                        Nhận vé điện tử ngay sau khi thanh toán, không cần in vé.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row py-5 text-center">
        <div class="col-md-3">
            <div class="display-4 fw-bold text-primary">{{ \App\Models\Movie::count() }}</div>
            <p class="text-muted">Phim đang chiếu</p>
        </div>
        <div class="col-md-3">
            <div class="display-4 fw-bold text-success">{{ \App\Models\Cinema::count() }}</div>
            <p class="text-muted">Rạp chiếu</p>
        </div>
        <div class="col-md-3">
            <div class="display-4 fw-bold text-info">{{ \App\Models\Theater::count() }}</div>
            <p class="text-muted">Phòng chiếu</p>
        </div>
        <div class="col-md-3">
            <div class="display-4 fw-bold text-warning">{{ \App\Models\User::where('role_id', \App\Models\Role::where('name', 'customer')->first()->id)->count() }}</div>
            <p class="text-muted">Khách hàng</p>
        </div>
    </div>
</div>
@endsection