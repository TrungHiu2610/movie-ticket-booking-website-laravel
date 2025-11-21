@extends('layouts.admin')

@section('title', 'Chỉnh sửa diễn viên')

@section('content')
    <div class="mb-4">
        <h2>Chỉnh sửa diễn viên</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.actors.index') }}">Diễn viên</a></li>
                <li class="breadcrumb-item active">Chỉnh sửa</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.actors.update', $actor) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Tên diễn viên <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $actor->name) }}" autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="photo_url" class="form-label">Ảnh diễn viên</label>
                            @if ($actor->photo_url)
                                <div class="mb-2">
                                    <img src="{{ $actor->photo_url }}" alt="{{ $actor->name }}" class="img-thumbnail"
                                        style="max-width: 200px;">
                                    <p class="text-muted small mt-1">Ảnh hiện tại</p>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('photo_url') is-invalid @enderror"
                                id="photo_url" name="photo_url" accept="image/jpeg,image/png,image/jpg,image/gif">
                            <small class="form-text text-muted">Chọn ảnh mới để thay đổi. Chấp nhận: JPEG, PNG, JPG, GIF.
                                Tối đa 2MB</small>
                            @error('photo_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="photoPreview" class="mt-2" style="display: none;">
                                <img id="previewImage" src="" alt="Preview" class="img-thumbnail"
                                    style="max-width: 200px;">
                                <p class="text-muted small mt-1">Ảnh mới (chưa lưu)</p>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Cập nhật
                            </button>
                            <a href="{{ route('admin.actors.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Hủy
                            </a>
                            <button type="button" class="btn btn-danger ms-auto"
                                onclick="if(confirm('Bạn có chắc muốn xóa diễn viên này?')) document.getElementById('delete-form').submit()">
                                <i class="bi bi-trash me-2"></i>Xóa diễn viên
                            </button>
                        </div>
                    </form>

                    <form id="delete-form" action="{{ route('admin.actors.destroy', $actor) }}" method="POST"
                        class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-clock-history me-2"></i>Thông tin
                    </h5>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2">
                            <strong>Ngày tạo:</strong><br>
                            {{ $actor->created_at->format('d/m/Y H:i') }}
                        </li>
                        <li>
                            <strong>Cập nhật:</strong><br>
                            {{ $actor->updated_at->format('d/m/Y H:i') }}
                        </li>
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
