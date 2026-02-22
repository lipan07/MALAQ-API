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
            return redirect()->back()
                ->withInput()
                ->with('error', 'Video duration must not exceed 3 minutes. Please upload a shorter video.');
        }
        $validated['video_path'] = $videoPath;

        EngloContent::create($validated);

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
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Video duration must not exceed 3 minutes. Please upload a shorter video.');
            }
            $videoService->deleteVideo($englo_content->video_path);
            $validated['video_path'] = $videoPath;
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
