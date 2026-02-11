@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('admin.support-tickets.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to Support Requests
            </a>
        </div>

        {{-- Support Request Details --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-chat-dots"></i> Support Request</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 140px;">Request ID</td>
                        <td><code>{{ $support_request->id }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Issue</td>
                        <td><span class="badge bg-primary">{{ $support_request->issue ?? '—' }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Message</td>
                        <td>{{ $support_request->message }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Submitted</td>
                        <td>{{ $support_request->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- User Details --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-person"></i> User Details</h5>
                @if($support_request->user && auth()->user()->hasPermissionTo('users'))
                <a href="{{ route('admin.users.show', $support_request->user->id) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-person"></i> Full profile
                </a>
                @endif
            </div>
            <div class="card-body">
                @if($support_request->user)
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted border-bottom pb-1">Basic info</h6>
                        <table class="table table-sm">
                            <tr>
                                <td class="text-muted" style="width: 140px;">Name</td>
                                <td>{{ $support_request->user->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email</td>
                                <td>{{ $support_request->user->email ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Phone</td>
                                <td>{{ $support_request->user->phone_no ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status</td>
                                <td>
                                    <span class="badge {{ $support_request->user->status === 'online' ? 'bg-success' : ($support_request->user->status === 'blocked' ? 'bg-danger' : 'bg-secondary') }}">
                                        {{ ucfirst($support_request->user->status ?? '—') }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">User ID</td>
                                <td><code class="small">{{ $support_request->user->id }}</code></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted border-bottom pb-1">Activity &amp; other</h6>
                        <table class="table table-sm">
                            <tr>
                                <td class="text-muted" style="width: 140px;">Last activity</td>
                                <td>{{ $support_request->user->last_activity ? \Carbon\Carbon::parse($support_request->user->last_activity)->format('M d, Y H:i') : '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Joined via invite</td>
                                <td>{{ $support_request->user->joined_via_invite ? 'Yes' : 'No' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Address</td>
                                <td>{{ Str::limit($support_request->user->address, 60) ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">About</td>
                                <td>{{ Str::limit($support_request->user->about_me, 80) ?: '—' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                @else
                <p class="text-muted mb-0">User no longer exists or was deleted.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
