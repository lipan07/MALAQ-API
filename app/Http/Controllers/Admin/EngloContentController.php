<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EngloGenre;
use App\Enums\EngloLanguage;
use App\Enums\EngloPodcastGenre;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEngloContentRequest;
use App\Http\Requests\UpdateEngloContentRequest;
use App\Models\EngloContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class EngloContentController extends Controller
{
    private const VIDEO_FOLDER = 'engloPoster';

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isSuperAdmin()) {
                abort(403, 'Only Super Admin can manage Englo posts.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50, 100]) ? $perPage : 15;
        $contents = EngloContent::orderByDesc('created_at')->paginate($perPage);

        return view('admin.englo-contents.index', compact('contents', 'perPage'));
    }

    public function create()
    {
        $genres = EngloGenre::cases();
        $languages = EngloLanguage::cases();
        $podcastGenres = EngloPodcastGenre::cases();

        return view('admin.englo-contents.create', compact('genres', 'languages', 'podcastGenres'));
    }

    public function store(StoreEngloContentRequest $request)
    {
        Log::info('EngloContentController@store: request reached', [
            'has_video' => $request->hasFile('video'),
        ]);

        try {
            $validated = $request->validated();
            unset($validated['video']);
            $validated['data'] = $this->parseData($validated['data'] ?? null);
            $this->normalizeEngloType($validated);

            $file = $request->file('video');
            if (!$file) {
                Log::warning('EngloContentController@store: no video file in request after validation');
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'No video file received. Check PHP upload limits (upload_max_filesize, post_max_size).');
            }

            Log::info('EngloContentController@store: storing video', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ]);

            [$videoPath, $storeError] = $this->storeVideo($file);
            if (!$videoPath) {
                Log::warning('EngloContentController@store: storeVideo failed', ['reason' => $storeError]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', $storeError ?: 'Video could not be saved.');
            }
            $validated['video_path'] = $videoPath;

            EngloContent::create($validated);
            Log::info('EngloContentController@store: created', ['video_path' => $videoPath]);

            return redirect()->route('admin.englo-contents.index')
                ->with('success', 'Englo post created successfully.');
        } catch (Throwable $e) {
            Log::error('EngloContentController@store: exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function edit(EngloContent $englo_content)
    {
        $genres = EngloGenre::cases();
        $languages = EngloLanguage::cases();
        $podcastGenres = EngloPodcastGenre::cases();
        $content = $englo_content;

        return view('admin.englo-contents.edit', compact('content', 'genres', 'languages', 'podcastGenres'));
    }

    public function update(UpdateEngloContentRequest $request, EngloContent $englo_content)
    {
        $validated = $request->validated();
        unset($validated['video']);
        $validated['data'] = $this->parseData($validated['data'] ?? null);
        $this->normalizeEngloType($validated);

        if ($request->hasFile('video')) {
            [$videoPath, $storeError] = $this->storeVideo($request->file('video'));
            if (!$videoPath) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $storeError ?: 'Video could not be saved.');
            }
            $this->deleteVideoFile($englo_content->video_path);
            $validated['video_path'] = $videoPath;
        }

        $englo_content->update($validated);

        return redirect()->route('admin.englo-contents.index')
            ->with('success', 'Englo post updated successfully.');
    }

    public function destroy(EngloContent $englo_content)
    {
        $this->deleteVideoFile($englo_content->video_path);
        $englo_content->delete();

        return redirect()->route('admin.englo-contents.index')
            ->with('success', 'Englo post deleted successfully.');
    }

    /**
     * Store video file in storage/app/public/engloPoster.
     * Returns [path, error]: path string on success, null + error message on failure.
     */
    private function storeVideo($file): array
    {
        if (!$file) {
            return [null, 'No video file received.'];
        }
        if (!$file->isValid()) {
            $code = $file->getError();
            $messages = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize in php.ini.',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds max size.',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
                UPLOAD_ERR_NO_FILE => 'No file uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temp folder.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload.',
            ];
            $msg = $messages[$code] ?? "Upload error (code {$code}).";
            Log::warning('EngloContentController@storeVideo: invalid file', ['error' => $code]);
            return [null, $msg];
        }

        $disk = Storage::disk('public');
        $dir = self::VIDEO_FOLDER;
        $rootPath = $disk->path('');

        try {
            if (!$disk->exists($dir)) {
                $disk->makeDirectory($dir);
                Log::info('EngloContentController@storeVideo: created directory', ['dir' => $dir]);
            }

            $fullDirPath = $disk->path($dir);
            if (!is_writable($fullDirPath)) {
                Log::warning('EngloContentController@storeVideo: directory not writable', ['path' => $fullDirPath]);
                return [null, 'Storage folder is not writable. Run: chmod -R 775 storage'];
            }

            $ext = strtolower($file->getClientOriginalExtension() ?: 'mp4');
            if (!in_array($ext, ['mp4', 'webm', 'mov'], true)) {
                $ext = 'mp4';
            }
            $name = Str::uuid() . '.' . $ext;
            $path = $file->storeAs($dir, $name, 'public');

            if (!$path || !$disk->exists($path)) {
                Log::warning('EngloContentController@storeVideo: storeAs failed or file missing', [
                    'path' => $path,
                    'root' => $rootPath,
                ]);
                return [null, 'Could not save file. Ensure storage/app/public/engloPoster is writable (chmod -R 775 storage).'];
            }

            Log::info('EngloContentController@storeVideo: saved', ['path' => $path]);
            return [$path, null];
        } catch (Throwable $e) {
            Log::error('EngloContentController@storeVideo: exception', [
                'message' => $e->getMessage(),
                'path' => $rootPath ?? null,
            ]);
            return [null, 'Save failed: ' . $e->getMessage() . '. Check storage permissions (chmod -R 775 storage).'];
        }
    }

    private function deleteVideoFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * When podcast_genre_id is set, clear genre_id and language_id; otherwise clear podcast_genre_id.
     */
    private function normalizeEngloType(array &$validated): void
    {
        if (!empty($validated['podcast_genre_id'])) {
            $validated['genre_id'] = null;
            $validated['language_id'] = null;
        } else {
            $validated['podcast_genre_id'] = null;
        }
    }

    private function parseData(mixed $data): ?array
    {
        if ($data === null || $data === '') {
            return null;
        }
        if (is_array($data)) {
            return $data;
        }
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            return is_array($decoded) ? $decoded : null;
        }
        return null;
    }
}
