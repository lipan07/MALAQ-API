@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-pencil"></i> Edit Category: {{ $category->name }}
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-info btn-sm">
                        <i class="bi bi-eye"></i> View
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to Categories
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $category->name) }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="guard_name" class="form-label">Guard Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('guard_name') is-invalid @enderror"
                                    id="guard_name" name="guard_name" value="{{ old('guard_name', $category->guard_name) }}"
                                    placeholder="e.g., cars, properties" required>
                                <div class="form-text">Used for API routing (lowercase, underscores only)</div>
                                @error('guard_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Parent Category</label>
                        <select class="form-select @error('parent_id') is-invalid @enderror"
                            id="parent_id" name="parent_id">
                            <option value="">Select Parent Category (Optional)</option>
                            @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}"
                                {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text">Leave empty to make this a root category</div>
                        @error('parent_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Category
                        </button>
                        <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-info">
                            <i class="bi bi-eye"></i> View Category
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Category Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>ID:</strong></td>
                        <td>{{ $category->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Created:</strong></td>
                        <td>{{ $category->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Updated:</strong></td>
                        <td>{{ $category->updated_at->format('M d, Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Posts Count:</strong></td>
                        <td>
                            <span class="badge bg-primary">{{ $category->posts_count ?? 0 }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Subcategories:</strong></td>
                        <td>
                            <span class="badge bg-success">{{ $category->children->count() }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Edit Guidelines</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        <strong>Guard Name:</strong> Changing this may affect API routes
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-info-circle text-info me-2"></i>
                        <strong>Parent:</strong> Changing parent affects category hierarchy
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Posts:</strong> Existing posts will be updated automatically
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection