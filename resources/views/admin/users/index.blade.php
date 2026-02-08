@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Users</h5>
        <button id="advancedFilterBtn" class="btn btn-secondary btn-sm">
            <i class="bi bi-funnel"></i> Advanced Filter
        </button>
    </div>

    <div id="advancedFilterPanel" class="p-3 border-bottom" style="display: none;">
        <div class="row g-2">
            <div class="col-md-3">
                <select id="statusFilter" class="form-select">
                    <option value="">All Status</option>
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control" placeholder="Search by name or email" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button id="applyFilterBtn" class="btn btn-primary btn-sm w-100">Apply</button>
            </div>
            <div class="col-md-2">
                <button id="resetFilterBtn" class="btn btn-outline-secondary btn-sm w-100">Reset</button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table table-hover" style="min-width: 800px;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone No</th>
                        <th>Status</th>
                        <th>Joined Via Invite</th>
                        <th>Last Activity</th>
                        <th>Created At</th>
                        <th>Invite Tokens</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $user)
                    <tr>
                        <td>{{ $users->firstItem() + $index }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email ?? '-' }}</td>
                        <td>{{ $user->phone_no ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $user->status === 'online' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td>
                            @if($user->joined_via_invite)
                            <span class="badge bg-info" title="User joined via invitation">
                                <i class="bi bi-gift"></i> Yes
                            </span>
                            @else
                            <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td>
                            {{ $user->last_activity ? \Carbon\Carbon::parse($user->last_activity)->diffForHumans() : '-' }}
                        </td>
                        <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            @if($user->joined_via_invite)
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#inviteTokensModal{{ $user->id }}" title="View Invite Tokens">
                                <i class="bi bi-gift"></i> Tokens
                            </button>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                {{-- Referral Tree button --}}
                                <a href="{{ route('admin.users.referral-tree', $user->id) }}" class="btn btn-info btn-sm" title="View Referral Tree">
                                    <i class="bi bi-diagram-3"></i> Tree
                                </a>

                                {{-- Block/Unblock button --}}
                                @if(auth()->user()->canBlockUsers())
                                @if($user->status === 'blocked')
                                <form action="{{ route('admin.users.unblock', $user->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-unlock"></i> Unblock
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.users.block', $user->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="bi bi-lock"></i> Block
                                    </button>
                                </form>
                                @endif
                                @endif

                                {{-- Actions dropdown --}}
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="dropdown-item">
                                                <i class="bi bi-eye"></i> View Details
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="dropdown-item">
                                                <i class="bi bi-pencil"></i> Edit User
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>

                                        @if(auth()->user()->canBlockUsers())
                                        @if($user->status !== 'blocked')
                                        <li>
                                            <form action="{{ route('admin.users.block', $user->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-warning">
                                                    <i class="bi bi-lock"></i> Block User
                                                </button>
                                            </form>
                                        </li>
                                        @else
                                        <li>
                                            <form action="{{ route('admin.users.unblock', $user->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-success">
                                                    <i class="bi bi-unlock"></i> Unblock User
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                        @endif

                                        @if(auth()->user()->canDeleteUsers())
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline delete-user-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger btn-delete-user">
                                                    <i class="bi bi-trash"></i> Delete User
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @include('admin.partials.per-page-pagination', ['paginator' => $users, 'perPage' => $perPage ?? 10])
    </div>
</div>

<!-- Invite Tokens Modal for each user -->
@foreach($users as $user)
<div class="modal fade" id="inviteTokensModal{{ $user->id }}" tabindex="-1" aria-labelledby="inviteTokensModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inviteTokensModalLabel{{ $user->id }}">
                    <i class="bi bi-gift"></i> Invite Tokens - {{ $user->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($user->inviteTokens && $user->inviteTokens->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Token</th>
                                <th>Status</th>
                                <th>Expires At</th>
                                <th>Used By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->inviteTokens as $token)
                            <tr>
                                <td>
                                    <code class="token-code">{{ $token->token }}</code>
                                </td>
                                <td>
                                    @if($token->is_used)
                                    <span class="badge bg-secondary">Used</span>
                                    @elseif($token->expires_at->isPast())
                                    <span class="badge bg-danger">Expired</span>
                                    @else
                                    <span class="badge bg-success">Active</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $token->expires_at->format('M d, Y H:i') }}</small>
                                </td>
                                <td>
                                    @if($token->usedBy)
                                    <small>{{ $token->usedBy->name }}<br>{{ $token->usedBy->email }}</small>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary copy-token-btn" data-token="{{ $token->token }}" title="Copy Token">
                                            <i class="bi bi-copy"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-info copy-url-btn" data-token="{{ $token->token }}" title="Copy URL">
                                            <i class="bi bi-link-45deg"></i>
                                        </button>
                                        @if(!$token->is_used)
                                        <button type="button" class="btn btn-outline-warning regenerate-token-btn" data-token-id="{{ $token->id }}" data-modal-id="inviteTokensModal{{ $user->id }}" title="Regenerate Token">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-gift" style="font-size: 3rem;"></i>
                    <p class="mt-2">No invite tokens found for this user.</p>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtn = document.getElementById('advancedFilterBtn');
        const filterPanel = document.getElementById('advancedFilterPanel');
        const applyBtn = document.getElementById('applyFilterBtn');
        const resetBtn = document.getElementById('resetFilterBtn');
        const statusSelect = document.getElementById('statusFilter');
        const searchInput = document.getElementById('searchInput');

        // Set filter values from query params
        statusSelect.value = "{{ request('status') }}";

        filterBtn.addEventListener('click', function() {
            filterPanel.style.display = filterPanel.style.display === 'none' ? 'block' : 'none';
        });

        applyBtn.addEventListener('click', function() {
            const selectedStatus = statusSelect.value;
            const search = searchInput.value;
            const url = new URL(window.location.href);

            if (selectedStatus) {
                url.searchParams.set('status', selectedStatus);
            } else {
                url.searchParams.delete('status');
            }

            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }

            window.location.href = url.toString();
        });

        resetBtn.addEventListener('click', function() {
            const url = new URL(window.location.href);
            url.searchParams.delete('status');
            url.searchParams.delete('search');
            window.location.href = url.toString();
        });

        // Delete confirmation
        document.querySelectorAll('.delete-user-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to delete this user?')) {
                    e.preventDefault();
                }
            });
        });

        // Copy token functionality
        document.querySelectorAll('.copy-token-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const token = this.getAttribute('data-token');
                navigator.clipboard.writeText(token).then(function() {
                    // Show feedback
                    const originalHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="bi bi-check"></i>';
                    btn.classList.remove('btn-outline-primary');
                    btn.classList.add('btn-success');
                    setTimeout(function() {
                        btn.innerHTML = originalHTML;
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-outline-primary');
                    }, 2000);
                }).catch(function(err) {
                    alert('Failed to copy token');
                });
            });
        });

        // Copy URL functionality
        document.querySelectorAll('.copy-url-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const token = this.getAttribute('data-token');
                const baseUrl = '{{ config("app.url", "https://nearx.co") }}';
                const inviteUrl = baseUrl + '/invite/' + token;
                navigator.clipboard.writeText(inviteUrl).then(function() {
                    // Show feedback
                    const originalHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="bi bi-check"></i>';
                    btn.classList.remove('btn-outline-info');
                    btn.classList.add('btn-success');
                    setTimeout(function() {
                        btn.innerHTML = originalHTML;
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-outline-info');
                    }, 2000);
                }).catch(function(err) {
                    alert('Failed to copy URL');
                });
            });
        });

        // Regenerate token functionality
        document.querySelectorAll('.regenerate-token-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const tokenId = this.getAttribute('data-token-id');
                const modalId = this.getAttribute('data-modal-id');
                const btnElement = this;

                if (!confirm('Are you sure you want to regenerate this token? The old token will be invalidated.')) {
                    return;
                }

                // Disable button and show loading
                btnElement.disabled = true;
                const originalHTML = btnElement.innerHTML;
                btnElement.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

                // Make AJAX request
                fetch(`/admin/invite-tokens/${tokenId}/regenerate`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            // Reload the page to show updated token
                            location.reload();
                        } else {
                            alert('Failed to regenerate token');
                            btnElement.disabled = false;
                            btnElement.innerHTML = originalHTML;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while regenerating the token');
                        btnElement.disabled = false;
                        btnElement.innerHTML = originalHTML;
                    });
            });
        });
    });
</script>
<style>
    .token-code {
        background-color: #f8f9fa;
        padding: 4px 8px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-weight: bold;
        color: #495057;
    }
</style>
@endpush