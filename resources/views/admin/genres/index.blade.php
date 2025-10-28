@extends('layouts.admin')

@section('title', 'Quản lý Thể loại')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý Thể loại</h2>
    <a href="{{ route('admin.genres.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Thêm thể loại mới
    </a>
</div>

@include('components.alert')

<!-- Search and Filter Component -->
<x-admin.search-filter
    searchPlaceholder="Tìm theo tên thể loại..."
    :filters="[]"
    :sortOptions="[
        'name' => 'Tên thể loại',
        'created_at' => 'Ngày tạo'
    ]" />

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 60%">Tên thể loại</th>
                        <th style="width: 15%">Ngày tạo</th>
                        <th style="width: 20%" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($genres as $genre)
                    <tr>
                        <td>{{ $genres->firstItem() + $loop->index }}</td>
                        <td>
                            <strong>{{ $genre->name }}</strong>
                        </td>
                        <td>{{ $genre->created_at->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.genres.edit', $genre) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.genres.destroy', $genre) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Bạn có chắc muốn xóa thể loại này?')">
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
                            Chưa có thể loại nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($genres->hasPages())
        <div class="mt-3">
            {{ $genres->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection