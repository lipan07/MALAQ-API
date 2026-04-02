<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdSettingController extends Controller
{
    public function index(): View
    {
        $settings = AdSetting::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.ad-settings.index', compact('settings'));
    }

    public function toggle(AdSetting $ad_setting): RedirectResponse
    {
        $ad_setting->update(['is_enabled' => ! $ad_setting->is_enabled]);

        return redirect()
            ->route('admin.ad-settings.index')
            ->with('success', 'Visibility saved.');
    }
}
