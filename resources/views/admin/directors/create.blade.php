@extends('layouts.admin')

@section('title', 'Thêm đạo diễn mới')

@section('content')
    <div class="mb-4">
        <h2>Thêm đạo diễn mới</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.directors.index') }}">Đạo diễn</a></li>
                <li class="breadcrumb-item active">Thêm mới</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.directors.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Tên đạo diễn <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}"
                                placeholder="Ví dụ: Christopher Nolan, Steven Spielberg..." autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="photo_url" class="form-label">Ảnh đạo diễn</label>
                            <input type="file" class="form-control @error('photo_url') is-invalid @enderror"
                                id="photo_url" name="photo_url" accept="image/jpeg,image/png,image/jpg,image/gif">
                            <small class="form-text text-muted">Chấp nhận: JPEG, PNG, JPG, GIF. Tối đa 2MB</small>
                            @error('photo_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="photoPreview" class="mt-2" style="display: none;">
                                <img id="previewImage" src="" alt="Preview" class="img-thumbnail"
                                    style="max-width: 200px;">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Lưu đạo diễn
                            </button>
                            <a href="{{ route('admin.directors.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-info-circle me-2"></i>Hướng dẫn
                    </h5>
                    <ul class="mb-0 small">
                        <li>Tên đạo diễn không được để trống</li>
                        <li>Tên không được vượt quá 255 ký tự</li>
                        <li>Nhập tên đầy đủ của đạo diễn</li>
                        <li>Ảnh là tùy chọn, tối đa 2MB</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Preview image before upload
        document.getElementById('photo_url').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImage').src = e.target.result;
                    document.getElementById('photoPreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('photoPreview').style.display = 'none';
            }
        });
    </script>
@endsection
