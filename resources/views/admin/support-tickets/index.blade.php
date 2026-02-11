@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-chat-dots"></i> Support Requests</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Issue</th>
                        <th>Message</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $index => $req)
                    <tr>
                        <td>{{ $requests->firstItem() + $index }}</td>
                        <td>
                            @if($req->user)
                            <span class="d-block fw-medium">{{ $req->user->name }}</span>
                            <small class="text-muted">{{ $req->user->email ?? $req->user->phone_no ?? '—' }}</small>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td><span class="badge bg-secondary">{{ $req->issue ?? '—' }}</span></td>
                        <td>{{ Str::limit($req->message, 50) }}</td>
                        <td>{{ $req->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.support-tickets.show', $req->id) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No support requests yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('admin.partials.per-page-pagination', ['paginator' => $requests, 'perPage' => $perPage ?? 15])
    </div>
</div>
@endsection
