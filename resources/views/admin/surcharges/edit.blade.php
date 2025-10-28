@extends('layouts.admin')

@section('title', 'Chỉnh sửa phụ thu')

@section('content')
<div class="mb-4">
    <h2>Chỉnh sửa phụ thu</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.surcharges.index') }}">Phụ thu</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.surcharges.update', $surcharge) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Tên phụ thu <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name', $surcharge->name) }}"
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
                                    <option value="fixed" {{ old('type', $surcharge->type) == 'fixed' ? 'selected' : '' }}>Cố định (đồng)</option>
                                    <option value="percentage" {{ old('type', $surcharge->type) == 'percentage' ? 'selected' : '' }}>Phần trăm (%)</option>
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label" id="amount-label">
                                    @if($surcharge->type == 'percentage')
                                    Phần trăm (%)
                                    @else
                                    Số tiền (đồng)
                                    @endif
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                    class="form-control @error('amount') is-invalid @enderror"
                                    id="amount"
                                    name="amount"
                                    value="{{ old('amount', $surcharge->amount) }}"
                                    step="0.01"
                                    min="0">
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
                            rows="3">{{ old('apply_condition', $surcharge->apply_condition) }}</textarea>
                        @error('apply_condition')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Cập nhật
                        </button>
                        <a href="{{ route('admin.surcharges.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>Hủy
                        </a>
                        <button type="button"
                            class="btn btn-danger ms-auto"
                            onclick="if(confirm('Bạn có chắc muốn xóa phụ thu này?')) document.getElementById('delete-form').submit()">
                            <i class="bi bi-trash me-2"></i>Xóa phụ thu
                        </button>
                    </div>
                </form>

                <form id="delete-form" action="{{ route('admin.surcharges.destroy', $surcharge) }}" method="POST" class="d-none">
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
                        {{ $surcharge->created_at->format('d/m/Y H:i') }}
                    </li>
                    <li>
                        <strong>Cập nhật:</strong><br>
                        {{ $surcharge->updated_at->format('d/m/Y H:i') }}
                    </li>
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