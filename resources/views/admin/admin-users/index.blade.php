@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Admin Users</h5>
        <a href="{{ route('admin.admin-users.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Add Admin User
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Invited</th>
                        <th>Created By</th>
                        <th>Permissions</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                    <tr>
                        <td>{{ $users->firstItem() + $index }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @php $roleBadges = ['super_admin' => 'bg-danger', 'admin' => 'bg-primary', 'lead' => 'bg-primary', 'moderator' => 'bg-info', 'support' => 'bg-warning text-dark', 'analyst' => 'bg-secondary', 'supervisor' => 'bg-info']; @endphp
                            @if(isset($roleBadges[$user->admin_role]))
                            <span class="badge {{ $roleBadges[$user->admin_role] }}">{{ \Illuminate\Support\Arr::get(config('roles.all_roles'), $user->admin_role, $user->admin_role) }}</span>
                            @else
                            <span class="badge bg-secondary">{{ $user->admin_role }}</span>
                            @endif
                        </td>
                        <td>
                            @if($user->admin_role === 'super_admin')
                            <span class="text-muted">—</span>
                            @elseif($user->joined_via_invite)
                            <span class="badge bg-success">Yes</span>
                            @else
                            <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td>{{ $user->createdBy?->name ?? '—' }}</td>
                        <td>
                            @if($user->isSuperAdmin())
                            <span class="text-muted">All</span>
                            @else
                            {{ $user->permissions->count() }} assigned
                            @endif
                        </td>
                        <td>
                            @if(!$user->isSuperAdmin())
                            <a href="{{ route('admin.admin-users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('admin.admin-users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this admin user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No admin users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('admin.partials.per-page-pagination', ['paginator' => $users, 'perPage' => $perPage ?? 15])
    </div>
</div>
@endsection