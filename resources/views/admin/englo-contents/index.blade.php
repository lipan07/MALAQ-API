@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-play-btn"></i> Englo Posts
                </h5>
                <a href="{{ route('admin.englo-contents.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Add Englo Post
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Video</th>
                                <th>Genre</th>
                                <th>Language</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contents as $item)
                            <tr>
                                <td>
                                    @if($item->video_path)
                                    <a href="{{ $item->video_url }}" target="_blank" rel="noopener" class="text-truncate d-inline-block" style="max-width: 220px;">
                                        <i class="bi bi-play-circle me-1"></i> View video
                                    </a>
                                    @else
                                    <span class="text-muted">–</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-secondary">{{ $item->genre()->label() }}</span></td>
                                <td><span class="badge bg-info">{{ $item->language()->label() }}</span></td>
                                <td>{{ $item->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <a href="{{ route('admin.englo-contents.edit', $item) }}" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.englo-contents.destroy', $item) }}" method="POST" class="d-inline englo-delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No Englo posts yet. <a href="{{ route('admin.englo-contents.create') }}">Add one</a>.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @include('admin.partials.per-page-pagination', ['paginator' => $contents, 'perPage' => $perPage])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.englo-delete-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to delete this Englo post?')) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush
