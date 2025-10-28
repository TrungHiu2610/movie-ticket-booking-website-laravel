@extends('layouts.admin')

@section('title', 'Thêm phụ thu mới')

@section('content')
<div class="mb-4">
    <h2>Thêm phụ thu mới</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.surcharges.index') }}">Phụ thu</a></li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.surcharges.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Tên phụ thu <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Ví dụ: Phụ thu cuối tuần, Phụ thu ngày lễ..."
                            autofocus>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Loại phụ thu <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror"
                                    id="type"
                                    name="type"
                                    onchange="toggleAmountLabel()">
                                    <option value="">-- Chọn loại --</option>
                                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Cố định (đồng)</option>
                                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Phần trăm (%)</option>
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label" id="amount-label">
                                    Số tiền <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                    class="form-control @error('amount') is-invalid @enderror"
                                    id="amount"
                                    name="amount"
                                    value="{{ old('amount') }}"
                                    step="0.01"
                                    min="0"
                                    placeholder="0">
                                @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="apply_condition" class="form-label">Điều kiện áp dụng <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('apply_condition') is-invalid @enderror"
                            id="apply_condition"
                            name="apply_condition"
                            rows="3"
                            placeholder="Ví dụ: Áp dụng cho tất cả các suất chiếu vào thứ 7, Chủ nhật">{{ old('apply_condition') }}</textarea>
                        @error('apply_condition')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Lưu phụ thu
                        </button>
                        <a href="{{ route('admin.surcharges.index') }}" class="btn btn-secondary">
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
                    <li><strong>Cố định:</strong> Số tiền cụ thể (VD: 20,000đ)</li>
                    <li><strong>Phần trăm:</strong> % của giá vé (VD: 10%)</li>
                    <li>Điều kiện áp dụng giúp phân biệt khi nào dùng phụ thu này</li>
                    <li>Ví dụ: "Cuối tuần", "Ngày lễ", "Suất chiếu đặc biệt"...</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleAmountLabel() {
        const type = document.getElementById('type').value;
        const label = document.getElementById('amount-label');
        if (type === 'percentage') {
            label.innerHTML = 'Phần trăm (%) <span class="text-danger">*</span>';
        } else if (type === 'fixed') {
            label.innerHTML = 'Số tiền (đồng) <span class="text-danger">*</span>';
        } else {
            label.innerHTML = 'Số tiền <span class="text-danger">*</span>';
        }
    }
</script>
@endsection