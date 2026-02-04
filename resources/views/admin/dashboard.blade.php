@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Dashboard</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-0">Welcome, {{ Auth::user()->name }}. Use the sidebar to navigate.</p>
    </div>
</div>
@endsection