@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>User Details</h5>
    </div>
    <div class="card-body">
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email ?? '-' }}</p>
        <p><strong>Phone No:</strong> {{ $user->phone_no ?? '-' }}</p>
        <p><strong>Status:</strong> <span class="badge {{ $user->status === 'online' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($user->status) }}</span></p>
        <p><strong>Last Activity:</strong> {{ $user->last_activity ? \Carbon\Carbon::parse($user->last_activity)->diffForHumans() : '-' }}</p>
        <p><strong>Created At:</strong> {{ $user->created_at->format('M d, Y H:i') }}</p>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm">Edit</a>
    </div>
</div>
@endsection