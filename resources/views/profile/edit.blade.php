@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="mb-4">Hồ sơ cá nhân</h2>

                @include('components.alert')

                <!-- Loyalty Points -->
                @if ($loyaltyPoints)
                    <div class="card mb-4"
                        style="border-left: 4px solid {{ $loyaltyPoints->currentTier->color ?? '#C0C0C0' }}">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Hạng thành viên</h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="badge text-white px-3 py-2 fs-6 me-3"
                                            style="background-color: {{ $loyaltyPoints->currentTier->color ?? '#C0C0C0' }}">
                                            {{ $loyaltyPoints->currentTier->name ?? 'Bạc' }}
                                        </div>
                                        <div>
                                            <div class="fs-4 fw-bold text-primary">
                                                {{ number_format($loyaltyPoints->total_points) }} điểm</div>
                                            <small class="text-muted">Tổng điểm tích lũy</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="fs-5 text-success fw-bold">
                                        Giảm giá: {{ $loyaltyPoints->currentTier->discount_percentage ?? 0 }}%
                                    </div>
                                    <small class="text-muted">Áp dụng cho mỗi lần đặt vé</small>
                                </div>
                            </div>

                            <div class="mt-3 pt-3 border-top">
                                <small class="text-muted">
                                    <strong>Cách tích điểm:</strong> Mỗi 1.000đ chi tiêu = 1 điểm
                                </small>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Profile Information -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Thông tin tài khoản</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="name" class="form-label">Họ và tên</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                                    <div class="alert alert-warning mt-2">
                                        <p class="mb-2">
                                            Địa chỉ email của bạn chưa được xác thực.
                                        </p>
                                        <form method="POST" action="{{ route('verification.send') }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning">
                                                Gửi lại email xác thực
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Update Password -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Đổi mật khẩu</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                    id="current_password" name="current_password">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu mới</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation">
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-key me-2"></i>Đổi mật khẩu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="card border-danger">
                    <div class="card-header bg-danger bg-opacity-10 border-danger">
                        <h5 class="mb-0 text-danger">Xóa tài khoản</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Khi tài khoản của bạn bị xóa, tất cả dữ liệu và tài nguyên của nó sẽ bị xóa vĩnh viễn.
                            Trước khi xóa tài khoản, vui lòng tải xuống bất kỳ dữ liệu hoặc thông tin nào bạn muốn giữ lại.
                        </p>

                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                            data-bs-target="#deleteAccountModal">
                            <i class="bi bi-trash me-2"></i>Xóa tài khoản
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-danger">
                    <h5 class="modal-title text-danger">Xác nhận xóa tài khoản</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')

                    <div class="modal-body">
                        <p class="text-muted">
                            Bạn có chắc chắn muốn xóa tài khoản của mình?
                            Khi tài khoản của bạn bị xóa, tất cả dữ liệu và tài nguyên của nó sẽ bị xóa vĩnh viễn.
                            Vui lòng nhập mật khẩu của bạn để xác nhận bạn muốn xóa vĩnh viễn tài khoản của mình.
                        </p>

                        <div class="mb-3">
                            <label for="password_delete" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password_delete" name="password" placeholder="Nhập mật khẩu để xác nhận" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-2"></i>Xóa tài khoản
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
