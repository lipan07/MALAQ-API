<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    /**
     * Search users that the current admin can impersonate (JSON for navbar dropdown).
     */
    public function search(Request $request)
    {
        if (!Auth::user()->canImpersonate()) {
            return response()->json([], 403);
        }

        $term = $request->input('q', '');
        $term = trim($term);
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $current = Auth::user();
        $query = User::where('id', '!=', $current->id)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            });

        if ($current->isSuperAdmin()) {
            // any user (admin or app user)
        } elseif ($current->isLead()) {
            $query->whereNotNull('admin_role')->where('admin_role', 'supervisor')->where('joined_via_invite', true);
        } elseif ($current->isOperationsAdmin()) {
            $query->whereNotNull('admin_role')->where('created_by_admin_id', $current->id);
        } else {
            return response()->json([]);
        }

        $users = $query->orderBy('name')->limit(20)->get(['id', 'name', 'email', 'admin_role']);

        $list = $users->map(function ($u) {
            $roleLabel = $u->admin_role
                ? \Illuminate\Support\Arr::get(config('roles.all_roles'), $u->admin_role, $u->admin_role)
                : 'App user';
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email ?? '—',
                'role' => $roleLabel,
            ];
        });

        return response()->json($list->toArray());
    }

    /**
     * Start impersonating a user.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->canImpersonate()) {
            abort(403, 'You do not have permission to impersonate users.');
        }

        $request->validate(['user_id' => 'required|uuid|exists:users,id']);

        $target = User::findOrFail($request->user_id);
        if (!Auth::user()->canImpersonateUser($target)) {
            abort(403, 'You cannot impersonate this user.');
        }

        $impersonator = Auth::user();
        session(['impersonator_id' => $impersonator->id]);
        Auth::login($target, false);

        return redirect()->route('admin.dashboard')->with('success', 'You are now viewing as ' . $target->name . '.');
    }

    /**
     * Stop impersonating and switch back to the original admin.
     */
    public function stop()
    {
        $impersonatorId = session('impersonator_id');
        if (!$impersonatorId) {
            return redirect()->route('admin.dashboard');
        }

        $impersonator = User::find($impersonatorId);
        session()->forget('impersonator_id');
        if ($impersonator) {
            Auth::login($impersonator, false);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Impersonation ended. You are back as ' . ($impersonator ? $impersonator->name : 'your account') . '.');
    }
}
