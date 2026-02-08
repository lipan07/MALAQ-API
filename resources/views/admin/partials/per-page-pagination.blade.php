@php
    $perPageOptions = $perPageOptions ?? [10, 25, 50, 100];
    $perPage = (int) ($perPage ?? 10);
    $options = array_values(array_unique(array_merge($perPageOptions, [$perPage])));
    sort($options);
    $from = $paginator->isEmpty() ? 0 : $paginator->firstItem();
    $to = $paginator->isEmpty() ? 0 : $paginator->lastItem();
    $total = $paginator->total();
    $perPageQuery = request()->except('page');
@endphp
<style>
.per-page-pagination-bar {
    --perpage-border: #e2e8f0;
    --perpage-bg: #ffffff;
    --perpage-hover: #f1f5f9;
    --perpage-active-bg: #3f51b5;
    --perpage-active-color: #fff;
    --perpage-radius: 8px;
}
.per-page-pagination-bar .entries-text {
    font-size: 0.875rem;
    color: #64748b;
}
.per-page-pagination-bar .per-page-block {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
}
.per-page-pagination-bar .per-page-label {
    font-size: 0.8125rem;
    font-weight: 500;
    color: #64748b;
    white-space: nowrap;
}
.per-page-pagination-bar .per-page-group {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.per-page-pagination-bar .per-page-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.5rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.8125rem;
    font-weight: 500;
    color: #475569;
    text-decoration: none;
    background: var(--perpage-bg);
    border: 1px solid var(--perpage-border);
    border-radius: var(--perpage-radius);
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
}
.per-page-pagination-bar .per-page-btn:hover {
    background: var(--perpage-hover);
    color: #1e293b;
    border-color: #cbd5e1;
}
.per-page-pagination-bar .per-page-btn.active {
    background: var(--perpage-active-bg);
    color: var(--perpage-active-color);
    border-color: var(--perpage-active-bg);
    pointer-events: none;
}
</style>
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mt-3 per-page-pagination-bar">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3 flex-wrap">
        <span class="entries-text">
            Showing {{ $from }} to {{ $to }} of {{ number_format($total) }} entries
        </span>
        <div class="per-page-block">
            <span class="per-page-label">Per page</span>
            <div class="per-page-group">
                @foreach($options as $opt)
                    @php
                        $url = request()->fullUrlWithQuery(array_merge($perPageQuery, ['per_page' => $opt, 'page' => null]));
                    @endphp
                    <a class="per-page-btn {{ $perPage == $opt ? 'active' : '' }}" href="{{ $url }}">{{ $opt }}</a>
                @endforeach
            </div>
        </div>
    </div>
    <div>
        {{ $paginator->withQueryString()->onEachSide(3)->links('pagination::bootstrap-5') }}
    </div>
</div>
