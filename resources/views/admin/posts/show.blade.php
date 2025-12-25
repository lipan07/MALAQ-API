@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Post Details</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to Posts
                    </a>
                    @if($post->status !== 'active')
                    <form action="{{ route('admin.posts.changeStatus', $post->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="status" value="active">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-check-circle"></i> Approve
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Post Information -->
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">Basic Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>ID:</strong></td>
                                        <td>{{ $post->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Title:</strong></td>
                                        <td>{{ $post->title }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Category:</strong></td>
                                        <td>
                                            @if($post->category)
                                            <span class="badge bg-primary">{{ $post->category->name }}</span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Type:</strong></td>
                                        <td>
                                            @if($post->type)
                                            <span class="badge bg-info">{{ ucfirst($post->type->value) }}</span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Amount:</strong></td>
                                        <td>
                                            @if($post->amount)
                                            <span class="fw-bold text-success">₹{{ number_format($post->amount, 2) }}</span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
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
                                    </tr>
                                    <tr>
                                        <td><strong>Show Phone:</strong></td>
                                        <td>
                                            <span class="badge {{ $post->show_phone ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $post->show_phone ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Location & Timing</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Address:</strong></td>
                                        <td>{{ $post->address ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Latitude:</strong></td>
                                        <td>{{ $post->latitude ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Longitude:</strong></td>
                                        <td>{{ $post->longitude ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Post Time:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($post->post_time)->format('M d, Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created:</strong></td>
                                        <td>{{ $post->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Updated:</strong></td>
                                        <td>{{ $post->updated_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Post Details -->
                        @if($postDetails)
                        <div class="mt-4">
                            <h6 class="text-muted">Category Specific Details</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    @foreach($postDetails->toArray() as $key => $value)
                                    @if($key !== 'id' && $key !== 'post_id' && $key !== 'created_at' && $key !== 'updated_at' && $value !== null)
                                    <tr>
                                        <td class="fw-bold">{{ ucwords(str_replace('_', ' ', $key)) }}:</td>
                                        <td>
                                            @if(is_bool($value))
                                            <span class="badge {{ $value ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $value ? 'Yes' : 'No' }}
                                            </span>
                                            @else
                                            {{ $value }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- User Information -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">User Information</h6>
                            </div>
                            <div class="card-body">
                                @if($post->user)
                                <div class="text-center mb-3">
                                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="bi bi-person-fill text-white" style="font-size: 24px;"></i>
                                    </div>
                                </div>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $post->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $post->user->email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{{ $post->user->phone_no ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge {{ $post->user->status === 'online' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ ucfirst($post->user->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Last Activity:</strong></td>
                                        <td>
                                            {{ $post->user->last_activity ? \Carbon\Carbon::parse($post->user->last_activity)->diffForHumans() : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Joined:</strong></td>
                                        <td>{{ $post->user->created_at->format('M d, Y') }}</td>
                                    </tr>
                                </table>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.users.show', $post->user->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i> View User Profile
                                    </a>
                                </div>
                                @else
                                <div class="text-center text-muted">
                                    <i class="bi bi-person-x" style="font-size: 48px;"></i>
                                    <p>User not found</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Images Section -->
                @php
                    $images = is_array($post->images) ? $post->images : [];
                    $imageCount = count($images);
                @endphp
                @if($imageCount > 0)
                <div class="mt-4">
                    <h6 class="text-muted">Post Images</h6>
                    <div class="row">
                        @foreach($images as $imageUrl)
                        @php
                            $url = is_string($imageUrl) ? $imageUrl : (is_object($imageUrl) && isset($imageUrl->url) ? $imageUrl->url : null);
                        @endphp
                        @if($url)
                        <div class="col-md-3 col-sm-4 col-6 mb-3">
                            <div class="card">
                                <img src="{{ $url }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Post Image">
                                <div class="card-body p-2">
                                    <small class="text-muted">{{ $loop->iteration }} of {{ $imageCount }}</small>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="mt-4 d-flex gap-2 flex-wrap">
                    @php
                    $statuses = [
                    'pending' => ['warning', 'bi-clock'],
                    'processing' => ['info', 'bi-gear'],
                    'active' => ['success', 'bi-check-circle'],
                    'inactive' => ['secondary', 'bi-pause-circle'],
                    'failed' => ['danger', 'bi-x-circle'],
                    'sold' => ['primary', 'bi-tag'],
                    'blocked' => ['dark', 'bi-ban'],
                    ];
                    @endphp

                    @foreach($statuses as $status => $config)
                    @if($post->status !== $status)
                    <form action="{{ route('admin.posts.changeStatus', $post->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="status" value="{{ $status }}">
                        <button type="submit" class="btn btn-outline-{{ $config[0] }} btn-sm">
                            <i class="bi {{ $config[1] }}"></i> Mark as {{ ucfirst($status) }}
                        </button>
                    </form>
                    @endif
                    @endforeach

                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#reportModal">
                        <i class="bi bi-flag"></i> Report Post
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.posts.report', $post->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Report Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <select name="reason" class="form-select" required>
                            <option value="">Select a reason</option>
                            <option value="Inappropriate Content">Inappropriate Content</option>
                            <option value="Spam">Spam</option>
                            <option value="Fake Information">Fake Information</option>
                            <option value="Duplicate Post">Duplicate Post</option>
                            <option value="Violation of Terms">Violation of Terms</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Additional details (optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Report & Block</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection