@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit Admin User</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.admin-users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
            <div class="mb-3">
                <span class="text-muted">Role: </span>
                @php $roleLabels = config('roles.all_roles'); @endphp
                @if($user->admin_role === 'super_admin')
                <span class="badge bg-danger">Super Admin</span>
                @else
                <span class="badge bg-secondary">{{ $roleLabels[$user->admin_role] ?? $user->admin_role }}</span>
                @endif
            </div>
            @if($showInvitedCheckbox)
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_invited_admin" value="1" id="is_invited_admin" {{ old('is_invited_admin', $user->joined_via_invite) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_invited_admin">Invited user</label>
                </div>
                <small class="text-muted">Invited lead/supervisor can only see and add invited users.</small>
            </div>
            @endif
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isLead() || (auth()->user()->isOperationsAdmin() && $roleIsStaff))
            <div class="mb-3">
                <label class="form-label">Permissions</label>
                @if(auth()->user()->isLead())
                <p class="small text-muted mb-1">You can only assign permissions that you have. Select which to give to this supervisor.</p>
                @elseif($roleIsStaff)
                <p class="small text-muted mb-1">Operations Manager can adjust permissions for Admin/Moderator/Support/Analyst.</p>
                @endif
                <div class="border rounded p-3">
                    @foreach($permissions as $p)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $p->id }}" id="perm{{ $p->id }}" {{ $user->permissions->contains($p) ? 'checked' : '' }}>
                        <label class="form-check-label" for="perm{{ $p->id }}">{{ $p->name }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.admin-users.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection