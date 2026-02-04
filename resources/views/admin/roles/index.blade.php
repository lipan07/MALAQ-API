@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Predefined Roles</h5>
    </div>
    <div class="card-body">
        <p class="text-muted">Roles are predefined. Only super admin can create lead/supervisor users and assign permissions from Admin Users.</p>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td>
                            @if($role['key'] === 'super_admin')
                                <span class="badge bg-danger">Super Admin</span>
                            @elseif($role['key'] === 'lead')
                                <span class="badge bg-primary">Lead</span>
                            @else
                                <span class="badge bg-info">Supervisor</span>
                            @endif
                        </td>
                        <td>{{ $role['description'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
