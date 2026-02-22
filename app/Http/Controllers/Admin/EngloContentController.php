<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EngloGenre;
use App\Enums\EngloLanguage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEngloContentRequest;
use App\Http\Requests\UpdateEngloContentRequest;
use App\Models\EngloContent;
use App\Services\EngloVideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EngloContentController extends Controller
{
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

    public function store(StoreEngloContentRequest $request, EngloVideoService $videoService)
    {
        $validated = $request->validated();
        unset($validated['video']);
        $validated['data'] = $this->parseData($validated['data'] ?? null);

        $videoPath = $videoService->storeAndProcess($request->file('video'));
        if ($videoPath === null) {
            Log::warning('EngloContentController: store failed (storeAndProcess returned null)', [
                'original_name' => $request->file('video')->getClientOriginalName(),
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Video could not be saved. Ensure it is under 3 minutes and that storage is writable (run: php artisan storage:link if you have not).');
        }
        $validated['video_path'] = $videoPath;

        EngloContent::create($validated);
        Log::info('EngloContentController: englo post created', ['video_path' => $videoPath]);

        return redirect()->route('admin.englo-contents.index')
            ->with('success', 'Englo post created successfully.');
    }

    public function edit(EngloContent $englo_content)
    {
        $genres = EngloGenre::cases();
        $languages = EngloLanguage::cases();
        $content = $englo_content;

        return view('admin.englo-contents.edit', compact('content', 'genres', 'languages'));
    }

    public function update(UpdateEngloContentRequest $request, EngloContent $englo_content, EngloVideoService $videoService)
    {
        $validated = $request->validated();
        unset($validated['video']);
        $validated['data'] = $this->parseData($validated['data'] ?? null);

        if ($request->hasFile('video')) {
            $videoPath = $videoService->storeAndProcess($request->file('video'));
            if ($videoPath === null) {
                Log::warning('EngloContentController: update failed (storeAndProcess returned null)', [
                    'englo_content_id' => $englo_content->id,
                    'original_name' => $request->file('video')->getClientOriginalName(),
                ]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Video could not be saved. Ensure it is under 3 minutes and that storage is writable.');
            }
            $videoService->deleteVideo($englo_content->video_path);
            $validated['video_path'] = $videoPath;
            Log::info('EngloContentController: englo post video replaced', ['id' => $englo_content->id, 'video_path' => $videoPath]);
        }

        $englo_content->update($validated);

        return redirect()->route('admin.englo-contents.index')
            ->with('success', 'Englo post updated successfully.');
    }

    public function destroy(EngloContent $englo_content, EngloVideoService $videoService)
    {
        $videoService->deleteVideo($englo_content->video_path);
        $englo_content->delete();

        return redirect()->route('admin.englo-contents.index')
            ->with('success', 'Englo post deleted successfully.');
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
