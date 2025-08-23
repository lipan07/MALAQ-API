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
                        <th>Last Activity</th>
                        <th>Created At</th>
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
                            {{ $user->last_activity ? \Carbon\Carbon::parse($user->last_activity)->diffForHumans() : '-' }}
                        </td>
                        <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="dropdown-item">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="dropdown-item">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline delete-user-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger btn-delete-user">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-3">
            {{ $users->onEachSide(3)->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
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
    });
</script>
@endpush