@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-pencil"></i> Edit Englo Post
                </h5>
                <a href="{{ route('admin.englo-contents.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to Englo Posts
                </a>
            </div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                @endif
                <form action="{{ route('admin.englo-contents.update', $content) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="video" class="form-label">Video</label>
                        <input type="file" class="form-control @error('video') is-invalid @enderror"
                            id="video" name="video" accept="video/mp4,video/webm,video/quicktime">
                        @if($content->video_path)
                        <div class="form-text">Current: <a href="{{ $content->video_url }}" target="_blank" rel="noopener">View video</a>. Upload a new file to replace.</div>
                        @else
                        <div class="form-text">MP4, WebM or MOV. Stored as uploaded.</div>
                        @endif
                        @error('video')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="podcast_genre_id" class="form-label">Podcast genre (optional)</label>
                        <select class="form-select @error('podcast_genre_id') is-invalid @enderror" id="podcast_genre_id" name="podcast_genre_id">
                            <option value="">— None (Film content) —</option>
                            @foreach($podcastGenres as $podcast)
                            <option value="{{ $podcast->value }}" {{ old('podcast_genre_id', $content->podcast_genre_id) == $podcast->value ? 'selected' : '' }}>
                                {{ $podcast->name() }}
                            </option>
                            @endforeach
                        </select>
                        @error('podcast_genre_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row" id="film-fields">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="genre_id" class="form-label">Genre <span class="text-danger" id="genre-required">*</span></label>
                                <select class="form-select @error('genre_id') is-invalid @enderror" id="genre_id" name="genre_id">
                                    <option value="">Select genre</option>
                                    @foreach($genres as $genre)
                                    <option value="{{ $genre->value }}" {{ old('genre_id', $content->genre_id) == $genre->value ? 'selected' : '' }}>
                                        {{ $genre->label() }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('genre_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="language_id" class="form-label">Language <span class="text-danger" id="language-required">*</span></label>
                                <select class="form-select @error('language_id') is-invalid @enderror" id="language_id" name="language_id">
                                    <option value="">Select language</option>
                                    @foreach($languages as $lang)
                                    <option value="{{ $lang->value }}" {{ old('language_id', $content->language_id) == $lang->value ? 'selected' : '' }}>
                                        {{ $lang->label() }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('language_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="data" class="form-label">JSON Data (optional)</label>
                        <textarea class="form-control font-monospace @error('data') is-invalid @enderror"
                            id="data" name="data" rows="6" placeholder='{"key": "value"}'>{{ old('data', $content->data ? json_encode($content->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</textarea>
                        <div class="form-text">Valid JSON object. Leave empty if not needed.</div>
                        @error('data')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Englo Post
                        </button>
                        <a href="{{ route('admin.englo-contents.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Info</h6>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>ID:</strong> <code class="small">{{ $content->id }}</code></p>
                <p class="mb-1"><strong>Created:</strong> {{ $content->created_at->format('M d, Y H:i') }}</p>
                <p class="mb-0"><strong>Updated:</strong> {{ $content->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('podcast_genre_id').addEventListener('change', function() {
    var isPodcast = this.value !== '';
    var filmFields = document.getElementById('film-fields');
    filmFields.style.opacity = isPodcast ? '0.6' : '1';
    document.getElementById('genre_id').required = !isPodcast;
    document.getElementById('language_id').required = !isPodcast;
});
document.getElementById('podcast_genre_id').dispatchEvent(new Event('change'));
</script>
@endpush
@endsection
