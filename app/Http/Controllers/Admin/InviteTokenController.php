<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class InviteTokenController extends Controller
{
    /**
     * List all invite tokens by user: one row per user, two columns for 1st and 2nd token with countdown.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 15;
        $users = User::whereHas('inviteTokens')
            ->with(['inviteTokens' => function ($q) {
                $q->with('usedBy')->orderBy('created_at');
            }])
            ->orderBy('name')
            ->paginate($perPage);

        return view('admin.invite-tokens.index', compact('users', 'perPage'));
    }
}
