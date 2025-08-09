@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Posts</h5>
        <div>
            <a href="#" class="btn btn-primary btn-sm">
                <i class="bi bi-plus"></i> Add New
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th class="d-none d-md-table-cell">User</th>
                        <th class="d-none d-sm-table-cell">Category</th>
                        <th>Status</th>
                        <th class="d-none d-lg-table-cell">Post Time</th>
                        <th>Images</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $post)
                    <tr>
                        <td>{{ $post->id }}</td>
                        <td>{{ Str::limit($post->title, 30) }}</td>
                        <td class="d-none d-md-table-cell">{{ $post->user?->name }}</td>
                        <td class="d-none d-sm-table-cell">
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
                        <td class="d-none d-lg-table-cell">
                            {{ \Carbon\Carbon::parse($post->post_time)->format('M d, Y H:i') }}
                        </td>
                        <td>
                            @if($post->images->count())
                            <button class="btn btn-sm btn-info view-images-btn"
                                data-images="{{ json_encode($post->images->pluck('url')) }}">
                                <i class="bi bi-images d-none d-sm-inline"></i> 
                                <span class="d-inline d-sm-none">View</span>
                                <span class="badge bg-white text-dark ms-1">{{ $post->images->count() }}</span>
                            </button>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="#" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="bi bi-pencil d-none d-sm-inline"></i>
                                    <span class="d-inline d-sm-none">Edit</span>
                                </a>
                                <a href="{{ route('admin.posts.approve', $post->id) }}" 
                                   class="btn btn-sm btn-success" title="Approve">
                                    <i class="bi bi-check-circle d-none d-sm-inline"></i>
                                    <span class="d-inline d-sm-none">Approve</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $posts->links() }}
        </div>
    </div>
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
</script>
@endpush

@push('styles')
<style>
    /* Responsive table adjustments */
    @media (max-width: 767.98px) {
        .table-responsive {
            border: 0;
        }
        .table thead {
            display: none;
        }
        .table tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
        }
        .table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            border-bottom: 1px solid #dee2e6;
        }
        .table td:before {
            content: attr(data-label);
            font-weight: bold;
            margin-right: 1rem;
        }
        .table td:last-child {
            border-bottom: 0;
        }
    }

    /* Image modal enhancements */
    .carousel-control-prev, .carousel-control-next {
        width: auto;
        opacity: 0.8;
    }
    .carousel-control-prev:hover, .carousel-control-next:hover {
        opacity: 1;
    }
    .object-fit-contain {
        object-fit: contain;
    }
</style>
@endpush