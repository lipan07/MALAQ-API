@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to Reports
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-flag"></i> Report Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 160px;">Report ID</td>
                        <td><code>{{ $report->id }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Type</td>
                        <td><span class="badge bg-primary">{{ $report->type ?? '—' }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Description</td>
                        <td>{{ $report->description ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Reported at</td>
                        <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Reported by</h6>
            </div>
            <div class="card-body">
                @if($report->reportingUser)
                <table class="table table-sm mb-0">
                    <tr><td class="text-muted" style="width: 140px;">Name</td><td>{{ $report->reportingUser->name }}</td></tr>
                    <tr><td class="text-muted">Email</td><td>{{ $report->reportingUser->email ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Phone</td><td>{{ $report->reportingUser->phone_no ?? '—' }}</td></tr>
                </table>
                @if(auth()->user()->hasPermissionTo('users'))
                <a href="{{ route('admin.users.show', $report->reportingUser->id) }}" class="btn btn-sm btn-outline-primary mt-2">View user profile</a>
                @endif
                @else
                <p class="text-muted mb-0">User no longer available.</p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Reported post</h6>
                @if($report->post && auth()->user()->hasPermissionTo('posts'))
                <a href="{{ route('admin.posts.show', $report->post->id) }}" class="btn btn-sm btn-primary">View post</a>
                @endif
            </div>
            <div class="card-body">
                @if($report->post)
                <table class="table table-sm mb-0">
                    <tr><td class="text-muted" style="width: 140px;">Title</td><td>{{ $report->post->title }}</td></tr>
                    <tr><td class="text-muted">Post ID</td><td><code>{{ $report->post->id }}</code></td></tr>
                    <tr><td class="text-muted">Owner</td><td>{{ $report->post->user ? $report->post->user->name : '—' }} {{ $report->post->user ? '(' . ($report->post->user->email ?? $report->post->user->phone_no ?? '') . ')' : '' }}</td></tr>
                    <tr><td class="text-muted">Status</td><td><span class="badge bg-secondary">{{ $report->post->status->value ?? $report->post->status ?? '—' }}</span></td></tr>
                </table>
                @else
                <p class="text-muted mb-0">Post no longer available or was deleted.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
