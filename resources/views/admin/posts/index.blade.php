@extends('admin.layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Posts</h5>
    </div>

    {{-- Status filter: buttons at top --}}
    @php
        $statusList = [
            '' => ['label' => 'All', 'class' => 'outline-secondary', 'icon' => 'bi-grid-3x3-gap'],
            'pending' => ['label' => 'Pending', 'class' => 'warning', 'icon' => 'bi-clock'],
            'processing' => ['label' => 'Processing', 'class' => 'info', 'icon' => 'bi-gear'],
            'active' => ['label' => 'Active', 'class' => 'success', 'icon' => 'bi-check-circle'],
            'inactive' => ['label' => 'Inactive', 'class' => 'secondary', 'icon' => 'bi-pause-circle'],
            'failed' => ['label' => 'Failed', 'class' => 'danger', 'icon' => 'bi-x-circle'],
            'sold' => ['label' => 'Sold', 'class' => 'primary', 'icon' => 'bi-tag'],
            'blocked' => ['label' => 'Blocked', 'class' => 'dark', 'icon' => 'bi-ban'],
        ];
        $currentStatus = request('status', '');
    @endphp
    <div class="card-body border-bottom bg-light">
        <p class="text-muted small mb-2">Filter by status</p>
        <div class="d-flex flex-wrap gap-2">
            @foreach($statusList as $value => $config)
            <a href="{{ request()->fullUrlWithQuery(array_merge(request()->except('page'), ['status' => $value ?: null])) }}"
               class="btn btn-{{ $currentStatus === (string)$value ? $config['class'] : 'outline-' . $config['class'] }} btn-sm">
                <i class="bi {{ $config['icon'] }} me-1"></i>{{ $config['label'] }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- Date range + Search + Category --}}
    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('admin.posts.index') }}" id="postsFilterForm" class="row g-3 align-items-end">
            @if(request()->has('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            @if(request()->has('per_page'))<input type="hidden" name="per_page" value="{{ request('per_page') }}">@endif

            <div class="col-12 col-md-4">
                <label class="form-label small text-muted">Date range (post time)</label>
                <input type="text" id="dateRangePicker" class="form-control form-control-sm"
                       placeholder="Select date range" value="{{ request('date_from') && request('date_to') ? request('date_from') . ' to ' . request('date_to') : '' }}" readonly>
                <input type="hidden" name="date_from" id="dateFrom" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to" id="dateTo" value="{{ request('date_to') }}">
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Search (title, email, category)</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..."
                       value="{{ request('search') }}">
            </div>

            <div class="col-12 col-md-2">
                <label class="form-label small text-muted">Category</label>
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">All categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->parent ? $cat->parent->name . ' » ' . $cat->name : $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-3 d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel"></i> Apply filters
                </button>
                @php
                    $clearParams = [];
                    if (request()->filled('status')) { $clearParams['status'] = request('status'); }
                    if (request()->filled('per_page')) { $clearParams['per_page'] = request('per_page'); }
                @endphp
                <a href="{{ route('admin.posts.index', $clearParams) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle"></i> Clear filters
                </a>
            </div>
        </form>
    </div>

    {{-- Card Body: table --}}
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table class="table table-hover" style="min-width: 800px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>User</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Post Time</th>
                        <th>Images</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $index => $post)
                    <tr>
                        <td>{{ $posts->firstItem() + $index }}</td>
                        <td>{{ Str::limit($post->title, 30) }}</td>
                        <td>
                            @if($post->user)
                            <span class="d-block">{{ $post->user->name }}</span>
                            <small class="text-muted">{{ $post->user->email ?? '—' }}</small>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($post->category)
                            <span class="badge bg-light text-dark">{{ $post->category?->name }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                            $status = $post->status->value ?? $post->status;
                            $statusClasses = [
                                'pending' => 'bg-warning text-dark',
                                'processing' => 'bg-info',
                                'active' => 'bg-success',
                                'inactive' => 'bg-secondary',
                                'failed' => 'bg-danger',
                                'sold' => 'bg-primary',
                                'blocked' => 'bg-dark',
                            ];
                            $statusClass = $statusClasses[$status] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ ucfirst($status) }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($post->post_time)->format('M d, Y H:i') }}</td>
                        <td>
                            @php
                            $images = is_array($post->images) ? $post->images : [];
                            $imageCount = count($images);
                            $imageUrls = array_map(function($img) {
                            return is_string($img) ? $img : (is_object($img) && isset($img->url) ? $img->url : null);
                            }, $images);
                            $imageUrls = array_filter($imageUrls);
                            @endphp
                            @if($imageCount > 0)
                            <button class="btn btn-sm btn-info view-images-btn"
                                data-images="{{ json_encode(array_values($imageUrls)) }}">
                                <i class="bi bi-images"></i>
                                <span class="badge bg-white text-dark ms-1">{{ $imageCount }}</span>
                            </button>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                @if($post->status->value !== 'active' && auth()->user()->canApproveListings())
                                <form action="{{ route('admin.posts.changeStatus', $post->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-check-circle"></i> Approve
                                    </button>
                                </form>
                                @endif

                                <a href="{{ route('admin.posts.show', $post->id) }}" class="btn btn-info btn-sm">
                                    <i class="bi bi-eye"></i> View
                                </a>

                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a href="{{ route('admin.posts.show', $post->id) }}" class="dropdown-item">
                                                <i class="bi bi-eye"></i> View Details
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>

                                        @php
                                        $statuses = [
                                        'pending' => ['warning', 'bi-clock'],
                                        'processing' => ['info', 'bi-gear'],
                                        'active' => ['success', 'bi-check-circle'],
                                        'inactive' => ['secondary', 'bi-pause-circle'],
                                        'failed' => ['danger', 'bi-x-circle'],
                                        'sold' => ['primary', 'bi-tag'],
                                        'blocked' => ['dark', 'bi-ban'],
                                        ];
                                        @endphp

                                        @foreach($statuses as $statusKey => $config)
                                        @if(($post->status->value ?? $post->status) !== $statusKey && auth()->user()->canApproveListings())
                                        <li>
                                            <form action="{{ route('admin.posts.changeStatus', $post->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="{{ $statusKey }}">
                                                <button type="submit" class="dropdown-item text-{{ $config[0] }}">
                                                    <i class="bi {{ $config[1] }}"></i> Mark as {{ ucfirst($statusKey) }}
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                        @endforeach

                                        @if(auth()->user()->canApproveListings())
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#reportModal{{ $post->id }}">
                                                <i class="bi bi-flag"></i> Report Post
                                            </button>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>

                            @if(auth()->user()->canApproveListings())
                            <div class="modal fade" id="reportModal{{ $post->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.posts.report', $post->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Report Post</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Reason</label>
                                                    <select name="reason" class="form-select" required>
                                                        <option value="">Select a reason</option>
                                                        <option value="Inappropriate Content">Inappropriate Content</option>
                                                        <option value="Spam">Spam</option>
                                                        <option value="Fake Information">Fake Information</option>
                                                        <option value="Duplicate Post">Duplicate Post</option>
                                                        <option value="Violation of Terms">Violation of Terms</option>
                                                        <option value="Other">Other</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="description" class="form-control" rows="3" placeholder="Additional details (optional)"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Report & Block</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Post Images</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner ratio ratio-16x9"></div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon bg-dark rounded-circle p-3"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon bg-dark rounded-circle p-3"></span>
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <span id="imageCounter" class="fw-bold">1 of 5</span>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.partials.per-page-pagination', ['paginator' => $posts, 'perPage' => $perPage ?? 10])
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date range picker
    const dateFromInput = document.getElementById('dateFrom');
    const dateToInput = document.getElementById('dateTo');
    const rangeInput = document.getElementById('dateRangePicker');

    if (rangeInput && dateFromInput && dateToInput) {
        function toYmd(d) {
            return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        }
        const picker = flatpickr(rangeInput, {
            mode: 'range',
            dateFormat: 'Y-m-d',
            allowInput: false,
            onChange: function(selectedDates) {
                if (selectedDates.length === 1) {
                    dateFromInput.value = toYmd(selectedDates[0]);
                    dateToInput.value = '';
                } else if (selectedDates.length === 2) {
                    dateFromInput.value = toYmd(selectedDates[0]);
                    dateToInput.value = toYmd(selectedDates[1]);
                }
            }
        });
        if (dateFromInput.value && dateToInput.value) {
            picker.setDate([dateFromInput.value, dateToInput.value], false);
        }
    }

    // Image modal
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    const carouselInner = document.querySelector('#imageCarousel .carousel-inner');
    const imageCounter = document.getElementById('imageCounter');

    document.querySelectorAll('.view-images-btn').forEach(button => {
        button.addEventListener('click', function() {
            const images = JSON.parse(this.getAttribute('data-images'));
            carouselInner.innerHTML = '';

            images.forEach((img, index) => {
                const div = document.createElement('div');
                div.classList.add('carousel-item', 'h-100');
                if (index === 0) div.classList.add('active');
                div.innerHTML = '<img src="' + img + '" class="d-block w-100 h-100 object-fit-contain" alt="Post Image">';
                carouselInner.appendChild(div);
            });

            imageCounter.textContent = '1 of ' + images.length;
            const carousel = new bootstrap.Carousel(document.getElementById('imageCarousel'), { interval: false });
            document.getElementById('imageCarousel').addEventListener('slid.bs.carousel', function(e) {
                imageCounter.textContent = (e.to + 1) + ' of ' + images.length;
            });
            imageModal.show();
        });
    });
});
</script>
@endpush
