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
                    @php
                        $badgeClass = [
                            'super_admin' => 'bg-danger',
                            'admin' => 'bg-primary',
                            'moderator' => 'bg-info',
                            'support' => 'bg-warning text-dark',
                            'analyst' => 'bg-secondary',
                            'lead' => 'bg-primary',
                            'supervisor' => 'bg-info',
                        ];
                    @endphp
                    @foreach($roles as $role)
                    <tr>
                        <td>
                            <span class="badge {{ $badgeClass[$role['key']] ?? 'bg-secondary' }}">{{ $role['name'] }}</span>
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
