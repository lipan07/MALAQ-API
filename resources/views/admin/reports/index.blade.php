@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-flag"></i> All Reports</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Reported by</th>
                        <th>Post</th>
                        <th>Post owner</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $index => $report)
                    <tr>
                        <td>{{ $reports->firstItem() + $index }}</td>
                        <td><span class="badge bg-secondary">{{ $report->type ?? '—' }}</span></td>
                        <td>{{ Str::limit($report->description, 40) ?: '—' }}</td>
                        <td>
                            @if($report->reportingUser)
                            <span class="d-block fw-medium">{{ $report->reportingUser->name }}</span>
                            <small class="text-muted">{{ $report->reportingUser->email ?? $report->reportingUser->phone_no ?? '—' }}</small>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($report->post)
                            {{ Str::limit($report->post->title, 30) }}
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($report->post && $report->post->user)
                            {{ $report->post->user->name }}
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No reports yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('admin.partials.per-page-pagination', ['paginator' => $reports, 'perPage' => $perPage ?? 15])
    </div>
</div>
@endsection
