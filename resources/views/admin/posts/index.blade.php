@extends('admin.layouts.app')

@section('content')
<div class="card">

    {{-- Card Header with Advanced Filter Button --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Posts</h5>
        <button id="advancedFilterBtn" class="btn btn-secondary btn-sm">
            <i class="bi bi-funnel"></i> Advanced Filter
        </button>
    </div>

    {{-- Advanced Filter Panel --}}
    <div id="advancedFilterPanel" class="p-3 border-bottom" style="display: none;">
        <div class="row g-2">
            <div class="col-md-3">
                <select id="statusFilter" class="form-select">
                    <option value="">All</option>
                    <!-- <option value="pending">Pending</option> -->
                    <option value="processing">Processing</option>
                    <!-- <option value="active">Active</option> -->
                    <!-- <option value="inactive">Inactive</option> -->
                    <!-- <option value="failed">Failed</option> -->
                    <option value="sold">Sold</option>
                    <option value="blocked">Blocked</option>
                </select>
            </div>
            <div class="col-md-2">
                <button id="applyFilterBtn" class="btn btn-primary btn-sm w-100">Apply</button>
            </div>
            <div class="col-md-2">
                <button id="resetFilterBtn" class="btn btn-outline-secondary btn-sm w-100">Reset</button>
            </div>
        </div>
    </div>

    {{-- Card Body --}}
    <div class="card-body">

        {{-- Flash Message --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- Scrollable Table for Mobile --}}
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
                        <td>{{ $post->user?->name }}</td>
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
                            $statusClass = strtolower($status) === 'pending' ? 'bg-warning text-dark' : 'bg-success';
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ ucfirst($status) }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($post->post_time)->format('M d, Y H:i') }}</td>
                        <td>
                            @if($post->images->count())
                            <button class="btn btn-sm btn-info view-images-btn"
                                data-images="{{ json_encode($post->images->pluck('url')) }}">
                                <i class="bi bi-images"></i>
                                <span class="badge bg-white text-dark ms-1">{{ $post->images->count() }}</span>
                            </button>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            {{-- Approve button --}}
                            <form action="{{ route('admin.posts.changeStatus', $post->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="status" value="active">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bi bi-check-circle"></i> Approve
                                </button>
                            </form>

                            {{-- More Actions dropdown --}}
                            <div class="btn-group">
                                <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots"></i> More Actions
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">

                                    @php
                                    $statuses = [
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'active' => 'success',
                                    'inactive' => 'secondary',
                                    'failed' => 'danger',
                                    'sold' => 'primary',
                                    'blocked' => 'dark',
                                    ];

                                    $statuses = [
                                    'processing' => 'info',
                                    'sold' => 'primary',
                                    'blocked' => 'danger',
                                    ];
                                    @endphp

                                    @foreach($statuses as $status => $color)
                                    <li>
                                        <form action="{{ route('admin.posts.changeStatus', $post->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="{{ $status }}">
                                            <button type="submit" class="dropdown-item text-{{ $color }}">
                                                <i class="bi bi-circle-fill"></i> {{ ucfirst($status) }}
                                            </button>
                                        </form>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <!-- Image Modal -->
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

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $posts->onEachSide(3)->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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

                    div.innerHTML = `
                        <img src="${img}" class="d-block w-100 h-100 object-fit-contain" alt="Post Image">
                    `;
                    carouselInner.appendChild(div);
                });

                // Update counter
                imageCounter.textContent = `1 of ${images.length}`;

                // Initialize carousel
                const carousel = new bootstrap.Carousel(document.getElementById('imageCarousel'), {
                    interval: false
                });

                // Update counter when slide changes
                document.getElementById('imageCarousel').addEventListener('slid.bs.carousel', function(e) {
                    const activeIndex = e.to;
                    imageCounter.textContent = `${activeIndex + 1} of ${images.length}`;
                });

                imageModal.show();
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const filterBtn = document.getElementById('advancedFilterBtn');
        const filterPanel = document.getElementById('advancedFilterPanel');
        const applyBtn = document.getElementById('applyFilterBtn');
        const resetBtn = document.getElementById('resetFilterBtn');
        const statusSelect = document.getElementById('statusFilter');

        // Toggle filter panel visibility
        filterBtn.addEventListener('click', function() {
            filterPanel.style.display = filterPanel.style.display === 'none' ? 'block' : 'none';
        });

        // Apply filter
        applyBtn.addEventListener('click', function() {
            const selectedStatus = statusSelect.value;
            const url = new URL(window.location.href);

            if (selectedStatus) {
                url.searchParams.set('status', selectedStatus);
            } else {
                url.searchParams.delete('status');
            }

            window.location.href = url.toString();
        });

        // Reset filter
        resetBtn.addEventListener('click', function() {
            const url = new URL(window.location.href);
            url.searchParams.delete('status');
            window.location.href = url.toString();
        });
    });
</script>
@endpush