<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EngloGenre;
use App\Enums\EngloLanguage;
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

        return view('admin.englo-contents.create', compact('genres', 'languages'));
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

            $videoPath = $this->storeVideo($file);
            if (!$videoPath) {
                Log::warning('EngloContentController@store: storeVideo returned null');
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Video could not be saved. Run: php artisan storage:link');
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
        $content = $englo_content;

        return view('admin.englo-contents.edit', compact('content', 'genres', 'languages'));
    }

    public function update(UpdateEngloContentRequest $request, EngloContent $englo_content)
    {
        $validated = $request->validated();
        unset($validated['video']);
        $validated['data'] = $this->parseData($validated['data'] ?? null);

        if ($request->hasFile('video')) {
            $videoPath = $this->storeVideo($request->file('video'));
            if (!$videoPath) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Video could not be saved. Run: php artisan storage:link');
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
     * Store video file in storage/app/public/engloPoster. Returns relative path or null.
     */
    private function storeVideo($file): ?string
    {
        if (!$file || !$file->isValid()) {
            Log::warning('EngloContentController@storeVideo: invalid or missing file', [
                'error' => $file ? $file->getError() : 'null',
            ]);
            return null;
        }

        $disk = Storage::disk('public');
        $dir = self::VIDEO_FOLDER;
        $root = $disk->path('');

        if (!$disk->exists($dir)) {
            $disk->makeDirectory($dir);
            Log::info('EngloContentController@storeVideo: created directory', ['dir' => $dir, 'root' => $root]);
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: 'mp4');
        if (!in_array($ext, ['mp4', 'webm', 'mov'], true)) {
            $ext = 'mp4';
        }
        $name = Str::uuid() . '.' . $ext;
        $path = $file->storeAs($dir, $name, 'public');

        Log::info('EngloContentController@storeVideo: result', [
            'path' => $path,
            'exists' => $path ? $disk->exists($path) : false,
        ]);

        return $path ?: null;
    }

    private function deleteVideoFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
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
