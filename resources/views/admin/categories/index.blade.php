@extends('admin.layouts.app')

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $stats['total_categories'] }}</h4>
                                <p class="mb-0">Total Categories</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-tags fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $stats['parent_categories'] }}</h4>
                                <p class="mb-0">Parent Categories</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-folder fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $stats['sub_categories'] }}</h4>
                                <p class="mb-0">Sub Categories</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-folder2-open fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $stats['categories_with_posts'] }}</h4>
                                <p class="mb-0">With Posts</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-file-post fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Management -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-tags"></i> Categories Management
                </h5>
                <div class="d-flex gap-2">
                    <button id="advancedFilterBtn" class="btn btn-secondary btn-sm">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    @if(auth()->user()->canManageCategoriesFull())
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> Add Category
                    </a>
                    @endif
                </div>
            </div>

            <!-- Advanced Filter Panel -->
            <div id="advancedFilterPanel" class="p-3 border-bottom" style="display: none;">
                <form method="GET" action="{{ route('admin.categories.index') }}">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search categories..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="parent_only" class="form-select">
                                <option value="">All Categories</option>
                                <option value="1" {{ request('parent_only') ? 'selected' : '' }}>Parent Categories Only</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Apply</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
                        </div>
                    </div>
                </form>
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
                                <th>ID</th>
                                <th>Name</th>
                                <th>Guard Name</th>
                                <th>Parent</th>
                                <th>Subcategories</th>
                                <th>Posts Count</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($category->parent_id)
                                        <i class="bi bi-arrow-return-right text-muted me-2"></i>
                                        @else
                                        <i class="bi bi-folder text-primary me-2"></i>
                                        @endif
                                        <strong>{{ $category->name }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $category->guard_name }}</code>
                                </td>
                                <td>
                                    @if($category->parent_id)
                                    <span class="badge bg-info">{{ $category->parent->name ?? 'Unknown' }}</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($category->children->count() > 0)
                                    <span class="badge bg-success">{{ $category->children->count() }} subcategories</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($category->posts_count > 0)
                                    <span class="badge bg-primary">{{ $category->posts_count }} posts</span>
                                    @else
                                    <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>{{ $category->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-info btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(auth()->user()->canManageCategoriesFull())
                                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline delete-category-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No categories found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @include('admin.partials.per-page-pagination', ['paginator' => $categories, 'perPage' => $perPage ?? 20])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtn = document.getElementById('advancedFilterBtn');
        const filterPanel = document.getElementById('advancedFilterPanel');

        // Toggle filter panel
        filterBtn.addEventListener('click', function() {
            filterPanel.style.display = filterPanel.style.display === 'none' ? 'block' : 'none';
        });

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