@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Posts</h5>
        <div>
            <a href="#" class="btn btn-primary btn-sm">
                Add New
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Post Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                    <tr>
                        <td>{{ $post->id }}</td>
                        <td>
                            <a href="#" class="text-primary text-decoration-none">
                                {{ Str::limit($post->title, 40) }}
                            </a>
                        </td>
                        <td>{{ $post->user?->name }}</td>
                        <td>
                            @if($post->category)
                            <span class="badge bg-light text-dark">
                                {{ $post->category?->name }}
                            </span>
                            @else
                            <span class="text-muted">Uncategorized</span>
                            @endif
                        </td>
                        <td>
                            @php
                            $status = $post->status->value ?? $post->status;
                            $statusClass = strtolower($status) === 'pending' ? 'status-pending' : 'status-approved';
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td>
                            @if($post->post_time instanceof \DateTime)
                            {{ $post->post_time->format('M d, Y H:i') }}
                            @else
                            {{ \Carbon\Carbon::parse($post->post_time)->format('M d, Y H:i') }}
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons d-flex gap-2">
                                <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                <a href="{{ route('admin.posts.approve', $post->id) }}" class="btn btn-sm btn-success">Approve</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-file-earmark-excel text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No posts found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($posts->hasPages())
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Page navigation">
                {{ $posts->onEachSide(1)->links() }}
            </nav>
        </div>
        @endif
    </div>
</div>
@endsection