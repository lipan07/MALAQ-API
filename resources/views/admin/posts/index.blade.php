@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Posts</h5>
        <div>
            <a href="#" class="btn btn-primary btn-sm">
                Add New
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
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
                        <th>Images</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Post Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                    <tr>
                        <td>{{ $post->id }}</td>
                        <td>
                            <a href="#" class="text-primary text-decoration-none">
                                {{ Str::limit($post->title, 40) }}
                            </a>
                        </td>
                        <td>
                            @if($post->images->count() > 0)
                            <div class="d-flex">
                                @foreach($post->images->take(3) as $image)
                                <img src="{{ asset($image->url) }}"
                                    class="img-thumbnail me-1"
                                    style="width: 40px; height: 40px; object-fit: cover; cursor: pointer;"
                                    onclick="showImageModal({{ $post->id }}, {{ $loop->index }})">
                                @endforeach
                                @if($post->images->count() > 3)
                                <span class="badge bg-primary align-self-center">+{{ $post->images->count() - 3 }}</span>
                                @endif
                            </div>
                            @else
                            <span class="text-muted">No images</span>
                            @endif
                        </td>
                        <td>{{ $post->user?->name }}</td>
                        <td>
                            @if($post->category)
                            <span class="badge bg-light text-dark">
                                {{ $post->category?->name }}
                            </span>
                            @else
                            <span class="text-muted">Uncategorized</span>
                            @endif
                        </td>
                        <td>
                            @php
                            $status = $post->status->value ?? $post->status;
                            $statusClass = strtolower($status) === 'pending' ? 'status-pending' : 'status-approved';
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td>
                            @if($post->post_time instanceof \DateTime)
                            {{ $post->post_time->format('M d, Y H:i') }}
                            @else
                            {{ \Carbon\Carbon::parse($post->post_time)->format('M d, Y H:i') }}
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons d-flex gap-2">
                                <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                <a href="{{ route('admin.posts.approve', $post->id) }}" class="btn btn-sm btn-success">Approve</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-file-earmark-excel text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No posts found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($posts->hasPages())
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Page navigation">
                {{ $posts->onEachSide(1)->links() }}
            </nav>
        </div>
        @endif
    </div>
</div>

<!-- Image Gallery Modal -->
<div class="modal fade" id="imageGalleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Post Images</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner" id="carousel-inner">
                        <!-- Images will be loaded here dynamically -->
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <span id="imageCounter" class="me-auto"></span>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Store all posts images data
    const postsImages = @json($posts -> keyBy('id') -> map(function($post) {
        return $post -> images -> map(function($image) {
            return asset($image -> path);
        });
    }));

    function showImageModal(postId, startIndex = 0) {
        const images = postsImages[postId] || [];
        const carouselInner = document.getElementById('carousel-inner');
        const imageCounter = document.getElementById('imageCounter');

        // Clear previous items
        carouselInner.innerHTML = '';

        // Add new items
        images.forEach((image, index) => {
            const item = document.createElement('div');
            item.className = `carousel-item ${index === startIndex ? 'active' : ''}`;
            item.innerHTML = `
                <img src="${image}" class="d-block w-100" style="max-height: 70vh; object-fit: contain;">
            `;
            carouselInner.appendChild(item);
        });

        // Update counter
        imageCounter.textContent = `${startIndex + 1} of ${images.length}`;

        // Initialize carousel if not already initialized
        const carousel = new bootstrap.Carousel(document.getElementById('imageCarousel'), {
            interval: false
        });

        // Update counter when slide changes
        document.getElementById('imageCarousel').addEventListener('slid.bs.carousel', function(e) {
            const activeIndex = e.to;
            imageCounter.textContent = `${activeIndex + 1} of ${images.length}`;
        });

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('imageGalleryModal'));
        modal.show();

        // Go to the clicked image
        if (images.length > 0) {
            carousel.to(startIndex);
        }
    }
</script>

<style>
    .carousel-item img {
        max-height: 70vh;
        object-fit: contain;
        margin: 0 auto;
    }

    .carousel-control-prev,
    .carousel-control-next {
        background-color: rgba(0, 0, 0, 0.2);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
    }

    .carousel-control-prev {
        left: 15px;
    }

    .carousel-control-next {
        right: 15px;
    }

    @media (max-width: 768px) {
        .carousel-item img {
            max-height: 50vh;
        }
    }
</style>
@endpush