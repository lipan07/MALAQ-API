@extends('admin.layouts.app')

@section('content')
<div class="card border-danger">
    <div class="card-body text-center py-5">
        <i class="bi bi-shield-x text-danger" style="font-size: 4rem;"></i>
        <h4 class="mt-3 text-danger">Permission Denied</h4>
        <p class="text-muted mb-4">You do not have permission to access this page. Contact your super admin to get access.</p>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
    </div>
</div>
@endsection