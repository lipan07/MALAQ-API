@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Invite Tokens</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Owner</th>
                        <th>1st Token</th>
                        <th>2nd Token</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php
                    $tokens = $user->inviteTokens;
                    $t1 = $tokens[0] ?? null;
                    $t2 = $tokens[1] ?? null;
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $user->name }}</div>
                            <div class="small text-muted">{{ $user->email ?? '—' }}</div>
                        </td>
                        <td class="align-top">
                            @if($t1)
                            <code class="user-select-all">{{ $t1->token }}</code>
                            <div class="mt-1">
                                @if($t1->is_used)
                                <span class="badge bg-secondary">Used</span>
                                @if($t1->usedBy)<span class="small text-muted">by {{ $t1->usedBy->name }}</span>@endif
                                @elseif($t1->expires_at->isPast())
                                <span class="badge bg-danger">Expired</span>
                                @else
                                <span class="invite-token-countdown small fw-semibold" data-expires-at="{{ $t1->expires_at->toIso8601String() }}">
                                    <span class="countdown-text">—</span>
                                </span>
                                @endif
                            </div>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="align-top">
                            @if($t2)
                            <code class="user-select-all">{{ $t2->token }}</code>
                            <div class="mt-1">
                                @if($t2->is_used)
                                <span class="badge bg-secondary">Used</span>
                                @if($t2->usedBy)<span class="small text-muted">by {{ $t2->usedBy->name }}</span>@endif
                                @elseif($t2->expires_at->isPast())
                                <span class="badge bg-danger">Expired</span>
                                @else
                                <span class="invite-token-countdown small fw-semibold" data-expires-at="{{ $t2->expires_at->toIso8601String() }}">
                                    <span class="countdown-text">—</span>
                                </span>
                                @endif
                            </div>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">No invite tokens found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('admin.partials.per-page-pagination', ['paginator' => $users, 'perPage' => $perPage ?? 15])
    </div>
</div>

@push('scripts')
<script>
    (function() {
        function updateCountdowns() {
            document.querySelectorAll('.invite-token-countdown').forEach(function(el) {
                var expiresAt = el.getAttribute('data-expires-at');
                if (!expiresAt) return;
                var end = new Date(expiresAt);
                var now = new Date();
                var span = el.querySelector('.countdown-text');
                if (!span) return;
                if (end <= now) {
                    span.textContent = 'Expired';
                    span.classList.remove('text-success', 'text-warning');
                    span.classList.add('text-danger');
                    return;
                }
                var diff = Math.floor((end - now) / 1000);
                var h = Math.floor(diff / 3600);
                var m = Math.floor((diff % 3600) / 60);
                var s = diff % 60;
                var parts = [];
                if (h > 0) parts.push(h + 'h');
                parts.push(String(m).padStart(2, '0') + 'm');
                parts.push(String(s).padStart(2, '0') + 's');
                span.textContent = parts.join(' ');
                span.classList.remove('text-danger');
                if (diff < 3600) {
                    span.classList.remove('text-success');
                    span.classList.add('text-warning');
                } else {
                    span.classList.remove('text-warning');
                    span.classList.add('text-success');
                }
            });
        }
        updateCountdowns();
        setInterval(updateCountdowns, 1000);
    })();
</script>
@endpush
@endsection