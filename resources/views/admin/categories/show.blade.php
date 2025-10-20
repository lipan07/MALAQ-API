@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-tag"></i> Category Details: {{ $category->name }}
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to Categories
                    </a>
                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Edit Category
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Category Information -->
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">Basic Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>ID:</strong></td>
                                        <td>{{ $category->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $category->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Guard Name:</strong></td>
                                        <td><code class="bg-light px-2 py-1 rounded">{{ $category->guard_name }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Parent Category:</strong></td>
                                        <td>
                                            @if($category->parent_id)
                                            <a href="{{ route('admin.categories.show', $category->parent_id) }}" class="text-decoration-none">
                                                <span class="badge bg-info">{{ $category->parent->name }}</span>
                                            </a>
                                            @else
                                            <span class="text-muted">Root Category</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created:</strong></td>
                                        <td>{{ $category->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Updated:</strong></td>
                                        <td>{{ $category->updated_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Statistics</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Total Posts:</strong></td>
                                        <td>
                                            <span class="badge bg-primary fs-6">{{ $posts->total() }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Subcategories:</strong></td>
                                        <td>
                                            <span class="badge bg-success fs-6">{{ $subcategories->count() }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Active Posts:</strong></td>
                                        <td>
                                            <span class="badge bg-success fs-6">{{ $category->posts()->where('status', 'active')->count() }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pending Posts:</strong></td>
                                        <td>
                                            <span class="badge bg-warning fs-6">{{ $category->posts()->where('status', 'pending')->count() }}</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Subcategories -->
                        @if($subcategories->count() > 0)
                        <div class="mt-4">
                            <h6 class="text-muted">Subcategories</h6>
                            <div class="row">
                                @foreach($subcategories as $subcategory)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">{{ $subcategory->name }}</h6>
                                                    <small class="text-muted">{{ $subcategory->posts_count }} posts</small>
                                                </div>
                                                <a href="{{ route('admin.categories.show', $subcategory->id) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Quick Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning">
                                        <i class="bi bi-pencil"></i> Edit Category
                                    </a>

                                    @if($category->posts_count == 0 && $subcategories->count() == 0)
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="delete-category-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="bi bi-trash"></i> Delete Category
                                        </button>
                                    </form>
                                    @endif

                                    <a href="{{ route('admin.posts.index', ['category' => $category->id]) }}" class="btn btn-info">
                                        <i class="bi bi-file-post"></i> View Posts
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Posts in this Category -->
                <div class="mt-4">
                    <h6 class="text-muted">Posts in this Category</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($posts as $post)
                                <tr>
                                    <td>{{ Str::limit($post->title, 30) }}</td>
                                    <td>{{ $post->user->name ?? 'Unknown' }}</td>
                                    <td>
                                        @php
                                        $status = $post->status->value ?? $post->status;
                                        $statusClass = match($status) {
                                        'pending' => 'bg-warning text-dark',
                                        'processing' => 'bg-info',
                                        'active' => 'bg-success',
                                        'inactive' => 'bg-secondary',
                                        'failed' => 'bg-danger',
                                        'sold' => 'bg-primary',
                                        'blocked' => 'bg-dark',
                                        default => 'bg-secondary'
                                        };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ ucfirst($status) }}</span>
                                    </td>
                                    <td>
                                        @if($post->amount)
                                        ₹{{ number_format($post->amount, 2) }}
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $post->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.posts.show', $post->id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No posts found in this category
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Posts Pagination -->
                    @if($posts->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $posts->onEachSide(3)->links('pagination::bootstrap-5') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete confirmation
        document.querySelectorAll('.delete-category-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush