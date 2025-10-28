@extends('layouts.admin')

@section('title', 'Quản lý Diễn viên')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý Diễn viên</h2>
    <a href="{{ route('admin.actors.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Thêm diễn viên mới
    </a>
</div>

@include('components.alert')

<!-- Search and Filter Component -->
<x-admin.search-filter
    searchPlaceholder="Tìm theo tên diễn viên..."
    :filters="[]"
    :sortOptions="[
        'name' => 'Tên diễn viên',
        'created_at' => 'Ngày tạo'
    ]" />

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 60%">Tên diễn viên</th>
                        <th style="width: 15%">Ngày tạo</th>
                        <th style="width: 20%" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($actors as $actor)
                    <tr>
                        <td>{{ $actors->firstItem() + $loop->index }}</td>
                        <td>
                            <strong>{{ $actor->name }}</strong>
                        </td>
                        <td>{{ $actor->created_at->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.actors.edit', $actor) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.actors.destroy', $actor) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Bạn có chắc muốn xóa diễn viên này?')">
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
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Chưa có diễn viên nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($actors->hasPages())
        <div class="mt-3">
            {{ $actors->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection