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
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="failed">Failed</option>
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
                                {{-- Approve button --}}
                                @if($post->status !== 'active')
                                <form action="{{ route('admin.posts.changeStatus', $post->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-check-circle"></i> Approve
                                    </button>
                                </form>
                                @endif

                                {{-- View Details button --}}
                                <a href="{{ route('admin.posts.show', $post->id) }}" class="btn btn-info btn-sm">
                                    <i class="bi bi-eye"></i> View
                                </a>

                                {{-- Actions dropdown --}}
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
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>

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

                                        @foreach($statuses as $status => $config)
                                        @if($post->status !== $status)
                                        <li>
                                            <form action="{{ route('admin.posts.changeStatus', $post->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="{{ $status }}">
                                                <button type="submit" class="dropdown-item text-{{ $config[0] }}">
                                                    <i class="bi {{ $config[1] }}"></i> Mark as {{ ucfirst($status) }}
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                        @endforeach

                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#reportModal{{ $post->id }}">
                                                <i class="bi bi-flag"></i> Report Post
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            {{-- Report Modal --}}
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