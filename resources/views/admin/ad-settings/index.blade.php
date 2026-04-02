@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-badge-ad"></i> In-app ad placements</h5>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <p class="text-muted small mb-3">Turn placements off to stop loading those ads in the mobile app. The app refreshes these settings about every 6 hours.</p>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Placement</th>
                        <th>Key (slug)</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($settings as $s)
                    <tr>
                        <td>{{ $s->label }}</td>
                        <td><code class="small">{{ $s->slug }}</code></td>
                        <td>
                            @if($s->is_enabled)
                            <span class="badge bg-success">On</span>
                            @else
                            <span class="badge bg-secondary">Off</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.ad-settings.toggle', $s) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $s->is_enabled ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                    @if($s->is_enabled)
                                    <i class="bi bi-toggle-off"></i> Turn off
                                    @else
                                    <i class="bi bi-toggle-on"></i> Turn on
                                    @endif
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No rows. Run <code>php artisan db:seed --class=AdSettingSeeder</code>.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
