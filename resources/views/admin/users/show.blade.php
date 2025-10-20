@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">User Details</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to Users
                    </a>
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil"></i> Edit User
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- User Profile Section -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-person-fill text-white" style="font-size: 32px;"></i>
                                </div>
                                <h5 class="mb-1">{{ $user->name }}</h5>
                                <p class="text-muted mb-3">{{ $user->email ?? 'No email provided' }}</p>

                                <div class="d-flex justify-content-center mb-3">
                                    <span class="badge {{ $user->status === 'online' ? 'bg-success' : ($user->status === 'blocked' ? 'bg-danger' : 'bg-secondary') }} fs-6">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    @if($user->status === 'blocked')
                                    <form action="{{ route('admin.users.unblock', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="bi bi-unlock"></i> Unblock User
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('admin.users.block', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            <i class="bi bi-lock"></i> Block User
                                        </button>
                                    </form>
                                    @endif

                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="delete-user-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i> Delete User
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Information -->
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">Basic Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>ID:</strong></td>
                                        <td>{{ $user->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $user->email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{{ $user->phone_no ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge {{ $user->status === 'online' ? 'bg-success' : ($user->status === 'blocked' ? 'bg-danger' : 'bg-secondary') }}">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Activity Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Last Activity:</strong></td>
                                        <td>
                                            {{ $user->last_activity ? \Carbon\Carbon::parse($user->last_activity)->diffForHumans() : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created:</strong></td>
                                        <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Updated:</strong></td>
                                        <td>{{ $user->updated_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Member Since:</strong></td>
                                        <td>{{ $user->created_at->diffForHumans() }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- User Posts -->
                        <div class="mt-4">
                            <h6 class="text-muted">User Posts</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($user->posts as $post)
                                        <tr>
                                            <td>{{ Str::limit($post->title, 30) }}</td>
                                            <td>
                                                @if($post->category)
                                                <span class="badge bg-light text-dark">{{ $post->category->name }}</span>
                                                @else
                                                <span class="text-muted">-</span>
                                                @endif
                                            </td>
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
                                            <td colspan="6" class="text-center text-muted">No posts found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
        document.querySelectorAll('.delete-user-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush