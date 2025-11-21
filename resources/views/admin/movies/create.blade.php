@extends('layouts.admin')

@section('title', 'Thêm Phim Mới')
@section('page-title', 'Thêm Phim Mới')

@section('content')
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.movies.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <!-- Title -->
                            <div class="col-md-6">
                                <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Duration -->
                            <div class="col-md-6">
                                <label for="duration_minutes" class="form-label">Thời lượng (phút) <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror"
                                    id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', 120) }}"
                                    min="1" required>
                                @error('duration_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Base Price -->
                            <div class="col-md-6">
                                <label for="base_price" class="form-label">Giá vé cơ bản (đồng) <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('base_price') is-invalid @enderror"
                                    id="base_price" name="base_price" value="{{ old('base_price', 100000) }}" min="0"
                                    step="1000" required>
                                @error('base_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">VD: Phim thường 100k, Blockbuster 120-130k</small>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="4" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Poster -->
                            <div class="col-md-6">
                                <label for="poster_url" class="form-label">Poster <span class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('poster_url') is-invalid @enderror"
                                    id="poster_url" name="poster_url" accept="image/*" required>
                                <small class="form-text text-muted">Chấp nhận: JPEG, PNG, JPG, GIF, WebP. Tối đa 5MB</small>
                                @error('poster_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="posterPreview" class="mt-2" style="display: none;">
                                    <img id="posterImage" src="" alt="Preview" class="img-thumbnail"
                                        style="max-width: 200px;">
                                </div>
                            </div>

                            <!-- Trailer - File or URL -->
                            <div class="col-md-6">
                                <label class="form-label">Trailer</label>
                                <div class="mb-2">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <input type="radio" class="btn-check" name="trailer_type" id="trailer_type_url"
                                            value="url" checked>
                                        <label class="btn btn-outline-primary" for="trailer_type_url">YouTube URL</label>

                                        <input type="radio" class="btn-check" name="trailer_type" id="trailer_type_file"
                                            value="file">
                                        <label class="btn btn-outline-primary" for="trailer_type_file">Upload Video</label>
                                    </div>
                                </div>

                                <div id="trailer_url_input">
                                    <input type="text" class="form-control @error('trailer_url') is-invalid @enderror"
                                        id="trailer_url_text" name="trailer_url" value="{{ old('trailer_url') }}"
                                        placeholder="https://www.youtube.com/watch?v=...">
                                    <small class="form-text text-muted">Paste YouTube hoặc video URL</small>
                                </div>

                                <div id="trailer_file_input" style="display: none;">
                                    <input type="file" class="form-control" id="trailer_url_file" name="trailer_url"
                                        accept="video/mp4,video/quicktime,video/x-msvideo">
                                    <small class="form-text text-muted">Chấp nhận: MP4, MOV, AVI. Tối đa 100MB</small>
                                </div>

                                @error('trailer_url')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Release Date -->
                            <div class="col-md-4">
                                <label for="release_date" class="form-label">Ngày phát hành <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('release_date') is-invalid @enderror"
                                    id="release_date" name="release_date" value="{{ old('release_date') }}" required>
                                @error('release_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Age Rating -->
                            <div class="col-md-4">
                                <label for="age_rating" class="form-label">Độ tuổi <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('age_rating') is-invalid @enderror" id="age_rating"
                                    name="age_rating" required>
                                    <option value="P" {{ old('age_rating') == 'P' ? 'selected' : '' }}>P - Phổ biến
                                    </option>
                                    <option value="C13" {{ old('age_rating') == 'C13' ? 'selected' : '' }}>C13 - Trên
                                        13 tuổi</option>
                                    <option value="C16" {{ old('age_rating') == 'C16' ? 'selected' : '' }}>C16 - Trên
                                        16 tuổi</option>
                                    <option value="C18" {{ old('age_rating') == 'C18' ? 'selected' : '' }}>C18 - Trên
                                        18 tuổi</option>
                                </select>
                                @error('age_rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-4">
                                <label for="status" class="form-label">Trạng thái <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status" required>
                                    <option value="coming_soon" {{ old('status') == 'coming_soon' ? 'selected' : '' }}>Sắp
                                        chiếu</option>
                                    <option value="now_showing" {{ old('status') == 'now_showing' ? 'selected' : '' }}>
                                        Đang chiếu</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Genres -->
                            <div class="col-12">
                                <label class="form-label">Thể loại</label>
                                <div class="border rounded p-3 @error('genres') is-invalid @enderror">
                                    <div class="row g-2">
                                        @foreach ($genres as $genre)
                                            <div class="col-md-3 col-sm-4 col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="genres[]"
                                                        value="{{ $genre->id }}" id="genre{{ $genre->id }}"
                                                        {{ in_array($genre->id, old('genres', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="genre{{ $genre->id }}">
                                                        {{ $genre->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @error('genres')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Actors -->
                            <div class="col-12">
                                <label class="form-label">Diễn viên</label>
                                <div class="border rounded p-3 @error('actors') is-invalid @enderror"
                                    style="max-height: 200px; overflow-y: auto;">
                                    <div class="row g-2">
                                        @foreach ($actors as $actor)
                                            <div class="col-md-3 col-sm-4 col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="actors[]"
                                                        value="{{ $actor->id }}" id="actor{{ $actor->id }}"
                                                        {{ in_array($actor->id, old('actors', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="actor{{ $actor->id }}">
                                                        {{ $actor->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @error('actors')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Directors -->
                            <div class="col-12">
                                <label class="form-label">Đạo diễn</label>
                                <div class="border rounded p-3 @error('directors') is-invalid @enderror">
                                    <div class="row g-2">
                                        @foreach ($directors as $director)
                                            <div class="col-md-3 col-sm-4 col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="directors[]"
                                                        value="{{ $director->id }}" id="director{{ $director->id }}"
                                                        {{ in_array($director->id, old('directors', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="director{{ $director->id }}">
                                                        {{ $director->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @error('directors')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Lưu phim
                            </button>
                            <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Poster preview
        document.getElementById('poster_url').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('posterImage').src = e.target.result;
                    document.getElementById('posterPreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('posterPreview').style.display = 'none';
            }
        });

        // Trailer type toggle
        document.querySelectorAll('input[name="trailer_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const urlInput = document.getElementById('trailer_url_input');
                const fileInput = document.getElementById('trailer_file_input');
                const urlField = document.getElementById('trailer_url_text');
                const fileField = document.getElementById('trailer_url_file');

                if (this.value === 'url') {
                    urlInput.style.display = 'block';
                    fileInput.style.display = 'none';
                    fileField.removeAttribute('name');
                    urlField.setAttribute('name', 'trailer_url');
                } else {
                    urlInput.style.display = 'none';
                    fileInput.style.display = 'block';
                    urlField.removeAttribute('name');
                    fileField.setAttribute('name', 'trailer_url');
                }
            });
        });
    </script>
@endsection
