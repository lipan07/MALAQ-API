@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Create Admin User</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.admin-users.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <select name="admin_role" class="form-select @error('admin_role') is-invalid @enderror" required>
                    <option value="">Select role</option>
                    @if($canCreateLead)<option value="lead" {{ old('admin_role') === 'lead' ? 'selected' : '' }}>Lead</option>@endif
                    @if($canCreateSupervisor)<option value="supervisor" {{ old('admin_role') === 'supervisor' ? 'selected' : '' }}>Supervisor</option>@endif
                </select>
                @error('admin_role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @if($forceInvitedSupervisor)
            <div class="alert alert-info py-2">
                <i class="bi bi-info-circle"></i> As an invited lead, you can only add invited supervisors. The new supervisor will be marked as invited.
            </div>
            @endif
            @if($showInvitedCheckbox)
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_invited_admin" value="1" id="is_invited_admin" {{ old('is_invited_admin') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_invited_admin">Invited user</label>
                </div>
                <small class="text-muted">Invited lead/supervisor can only see and add invited users (admin users and app users who joined via invite).</small>
            </div>
            @endif
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isLead())
            <div class="mb-3">
                <label class="form-label">Permissions</label>
                @if(auth()->user()->isLead())
                <p class="small text-muted mb-1">You can only assign permissions that you have. Select which to give to this supervisor.</p>
                @endif
                <div class="border rounded p-3">
                    @foreach($permissions as $p)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $p->id }}" id="perm{{ $p->id }}" {{ in_array($p->id, old('permissions', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="perm{{ $p->id }}">{{ $p->name }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('admin.admin-users.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection