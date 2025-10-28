@extends('layouts.admin')

@section('title', 'Thêm thể loại mới')

@section('content')
<div class="mb-4">
    <h2>Thêm thể loại mới</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.genres.index') }}">Thể loại</a></li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.genres.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Tên thể loại <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Ví dụ: Hành động, Kinh dị, Tình cảm..."
                            autofocus>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Lưu thể loại
                        </button>
                        <a href="{{ route('admin.genres.index') }}" class="btn btn-secondary">
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
                    <li>Tên thể loại không được để trống</li>
                    <li>Tên không được vượt quá 255 ký tự</li>
                    <li>Ví dụ: Hành động, Kinh dị, Khoa học viễn tưởng, Tình cảm, Hài hước...</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection