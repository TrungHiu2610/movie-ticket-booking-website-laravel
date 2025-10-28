@extends('layouts.admin')

@section('title', 'Quản lý Cụm rạp')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý Cụm rạp</h2>
    <a href="{{ route('admin.cinemas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Thêm cụm rạp mới
    </a>
</div>

@include('components.alert')

<!-- Search and Filter Component -->
<x-admin.search-filter
    searchPlaceholder="Tìm theo tên, thành phố, địa chỉ..."
    :filters="[
        [
            'name' => 'city',
            'label' => 'Thành phố',
            'col' => 3,
            'options' => $cities->toArray()
        ]
    ]"
    :sortOptions="[
        'name' => 'Tên cụm rạp',
        'city' => 'Thành phố',
        'created_at' => 'Ngày tạo'
    ]" />

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 25%">Tên cụm rạp</th>
                        <th style="width: 35%">Địa chỉ</th>
                        <th style="width: 15%">Thành phố</th>
                        <th style="width: 10%" class="text-center">Phòng chiếu</th>
                        <th style="width: 10%" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cinemas as $cinema)
                    <tr>
                        <td>{{ $cinemas->firstItem() + $loop->index }}</td>
                        <td>
                            <strong>{{ $cinema->name }}</strong>
                        </td>
                        <td>{{ $cinema->address }}</td>
                        <td>{{ $cinema->city }}</td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $cinema->theaters_count }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.cinemas.edit', $cinema) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.cinemas.destroy', $cinema) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Bạn có chắc muốn xóa cụm rạp này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Chưa có cụm rạp nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($cinemas->hasPages())
        <div class="mt-3">
            {{ $cinemas->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection