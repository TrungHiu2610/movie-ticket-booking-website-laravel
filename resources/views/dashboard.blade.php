@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Dashboard</h2>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Chào mừng, {{ auth()->user()->name }}!</h5>
                    <p class="card-text">Bạn đã đăng nhập thành công vào hệ thống.</p>

                    <hr>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Tính năng đặt vé đang được phát triển</strong>
                        <p class="mb-0 mt-2">Chức năng đặt vé xem phim sẽ sớm được cập nhật. Vui lòng quay lại sau!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection