@extends('admin.layouts.app')

@section('content')
<div class="mb-3">
    <h4 class="mb-1">Welcomes, {{ Auth::user()->name }}</h4>
    <p class="text-muted small mb-0">
        @if($isSuperAdmin)
        Super Admin dashboard – overview of all platform stats.
        @elseif($isLead)
        Lead dashboard – stats for invited users only.
        @else
        Dashboard – key metrics below.
        @endif
    </p>
</div>

<div class="row">
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 fw-bold text-primary">{{ number_format($stats['user_count']) }}</h3>
                    <p class="text-muted small mb-0">Users</p>
                    @if($isLead)<span class="badge bg-light text-dark mt-1">Invited</span>@endif
                </div>
                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                    <i class="bi bi-people fs-2 text-primary"></i>
                </div>
            </div>
            @if(auth()->user()->hasPermissionTo('users'))
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('admin.users.index') }}" class="small text-primary text-decoration-none">View users <i class="bi bi-arrow-right"></i></a>
            </div>
            @endif
        </div>
    </div>

    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 fw-bold text-success">{{ number_format($stats['post_count']) }}</h3>
                    <p class="text-muted small mb-0">Posts</p>
                    @if($isLead)<span class="badge bg-light text-dark mt-1">Invited</span>@endif
                </div>
                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                    <i class="bi bi-file-post fs-2 text-success"></i>
                </div>
            </div>
            @if(auth()->user()->hasPermissionTo('posts'))
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('admin.posts.index') }}" class="small text-success text-decoration-none">View posts <i class="bi bi-arrow-right"></i></a>
            </div>
            @endif
        </div>
    </div>

    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 fw-bold text-info">{{ number_format($stats['token_count']) }}</h3>
                    <p class="text-muted small mb-0">Invite Tokens</p>
                    @if($isLead)<span class="badge bg-light text-dark mt-1">Invited</span>@endif
                </div>
                <div class="rounded-circle bg-info bg-opacity-10 p-3">
                    <i class="bi bi-gift fs-2 text-info"></i>
                </div>
            </div>
            @if(auth()->user()->hasPermissionTo('all_invite_tokens'))
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('admin.invite-tokens.index') }}" class="small text-info text-decoration-none">View tokens <i class="bi bi-arrow-right"></i></a>
            </div>
            @endif
        </div>
    </div>

    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 fw-bold text-success">{{ number_format($stats['payment_confirmed']) }}</h3>
                    <p class="text-muted small mb-0">Payments Confirmed</p>
                    @if($isLead)<span class="badge bg-light text-dark mt-1">Invited</span>@endif
                </div>
                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                    <i class="bi bi-check-circle fs-2 text-success"></i>
                </div>
            </div>
            @if(auth()->user()->hasPermissionTo('payments'))
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('admin.payments.index') }}?status=confirmed" class="small text-success text-decoration-none">View confirmed <i class="bi bi-arrow-right"></i></a>
            </div>
            @endif
        </div>
    </div>

    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 fw-bold text-warning">{{ number_format($stats['payment_pending']) }}</h3>
                    <p class="text-muted small mb-0">Payments Pending</p>
                    @if($isLead)<span class="badge bg-light text-dark mt-1">Invited</span>@endif
                </div>
                <div class="rounded-circle bg-warning bg-opacity-25 p-3">
                    <i class="bi bi-clock-history fs-2 text-warning"></i>
                </div>
            </div>
            @if(auth()->user()->hasPermissionTo('payments'))
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('admin.payments.index') }}?status=pending" class="small text-warning text-decoration-none">View pending <i class="bi bi-arrow-right"></i></a>
            </div>
            @endif
        </div>
    </div>

    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 fw-bold text-secondary">{{ number_format($stats['support_count']) }}</h3>
                    <p class="text-muted small mb-0">Support Tickets</p>
                    @if($isLead)<span class="badge bg-light text-dark mt-1">Invited</span>@endif
                </div>
                <div class="rounded-circle bg-secondary bg-opacity-10 p-3">
                    <i class="bi bi-chat-dots fs-2 text-secondary"></i>
                </div>
            </div>
            @if(auth()->user()->hasPermissionTo('support_tickets'))
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('admin.support-tickets.index') }}" class="small text-secondary text-decoration-none">View tickets <i class="bi bi-arrow-right"></i></a>
            </div>
            @endif
        </div>
    </div>

    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 fw-bold text-danger">{{ number_format($stats['report_count']) }}</h3>
                    <p class="text-muted small mb-0">Reports</p>
                    @if($isLead)<span class="badge bg-light text-dark mt-1">Invited</span>@endif
                </div>
                <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                    <i class="bi bi-flag fs-2 text-danger"></i>
                </div>
            </div>
            @if(auth()->user()->hasPermissionTo('reports'))
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('admin.reports.index') }}" class="small text-danger text-decoration-none">View reports <i class="bi bi-arrow-right"></i></a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection