@extends('layouts.admin')

@section('title', 'Quản lý AI Embeddings')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="bi bi-database"></i> Quản lý AI Embeddings</h2>
                <p>Vector embeddings cho hệ thống AI chatbot</p>
            </div>
            <form action="{{ route('admin.embeddings.embed-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary"
                    onclick="return confirm('Embed tất cả phim? (Có thể mất vài phút)')">
                    <i class="bi bi-lightning-fill"></i> Embed Tất Cả Phim
                </button>
            </form>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Danh sách Embeddings</h5>
            </div>
            <div class="card-body">
                @if ($embeddings->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                        <p class=" mt-3">Chưa có embedding nào. Nhấn "Embed Tất Cả Phim" để bắt đầu.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="80">ID</th>
                                    <th>Phim</th>
                                    <th width="180">Embedded At</th>
                                    <th width="150" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($embeddings as $embedding)
                                    <tr>
                                        <td>{{ $embedding->id }}</td>
                                        <td>
                                            <strong>{{ $embedding->movie->title }}</strong>
                                            <br>
                                            <small class="">
                                                {{ Str::limit($embedding->content, 80) }}
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                {{ $embedding->embedded_at->format('d/m/Y H:i') }}
                                                <br>
                                                <span class="">{{ $embedding->embedded_at->diffForHumans() }}</span>
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-success"
                                                onclick="reEmbed({{ $embedding->movie_id }})" title="Re-embed phim này">
                                                <i class="bi bi-arrow-repeat"></i> Re-embed
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $embeddings->links() }}
                    </div>
                @endif
            </div>
            <div class="card-footer bg-light">
                <small class="">
                    <i class="bi bi-info-circle"></i>
                    Embedding sử dụng Gemini text-embedding-004 với 768 dimensions
                </small>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="">Tổng embeddings</h6>
                        <h3>{{ $embeddings->total() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="">Embedding gần đây</h6>
                        <h3>{{ $embeddings->where('embedded_at', '>=', now()->subDay())->count() }}</h3>
                        <small class="">24 giờ qua</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="">PostgreSQL pgvector</h6>
                        <h3><i class="bi bi-check-circle-fill text-success"></i> Active</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            async function reEmbed(movieId) {
                if (!confirm('Re-embed phim này?')) return;

                const btn = event.target.closest('button');
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang xử lý...';

                try {
                    const response = await fetch(`/admin/embeddings/embed/${movieId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('Embed thanh cong!');
                        location.reload();
                    } else {
                        alert('Embed that bai: ' + data.message);
                    }
                } catch (error) {
                    alert('Co loi xay ra!');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            }
        </script>
    @endpush
@endsection
