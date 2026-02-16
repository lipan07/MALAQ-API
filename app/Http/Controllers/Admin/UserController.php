<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /** Ensure this is an app user and invited admins can only access invited app users. */
    private function ensureInvitedAdminCanAccess(User $user): void
    {
        if ($user->admin_role !== null) {
            abort(404, 'User not found.');
        }
        if (request()->user()->isInvitedAdmin() && !$user->joined_via_invite) {
            abort(403, 'You can only view or manage invited users.');
        }
    }
    public function index(Request $request)
    {
        $users = User::with('inviteTokens')
            ->whereNull('admin_role'); // App users only (exclude admin panel users)

        // Invited lead/supervisor: only see app users who joined via invite
        if ($request->user()->isInvitedAdmin()) {
            $users->where('joined_via_invite', true);
        }

        // Advanced filter: status
        if ($request->filled('status')) {
            $users->where('status', $request->status);
        }

        // Advanced filter: search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $users->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $users = $users->orderBy('created_at', 'desc')->paginate($perPage);

        return view('admin.users.index', compact('users', 'perPage'));
    }

    public function show(User $user)
    {
        $this->ensureInvitedAdminCanAccess($user);
        $user->load('inviteTokens.usedBy');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->ensureInvitedAdminCanAccess($user);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureInvitedAdminCanAccess($user);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone_no' => 'nullable|string|max:20',
            'status' => 'required|in:online,offline',
        ]);

        $user->update($request->only(['name', 'email', 'phone_no', 'status']));

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function block(User $user)
    {
        if (!request()->user()->canBlockUsers()) {
            abort(403, 'You do not have permission to block users.');
        }
        $this->ensureInvitedAdminCanAccess($user);
        $user->update(['status' => 'blocked']);

        return back()->with('success', 'User has been blocked successfully.');
    }

    public function unblock(User $user)
    {
        if (!request()->user()->canBlockUsers()) {
            abort(403, 'You do not have permission to unblock users.');
        }
        $this->ensureInvitedAdminCanAccess($user);
        $user->update(['status' => 'online']);

        return back()->with('success', 'User has been unblocked successfully.');
    }

    public function destroy(User $user)
    {
        if (!request()->user()->canDeleteUsers()) {
            abort(403, 'You do not have permission to delete users.');
        }
        $this->ensureInvitedAdminCanAccess($user);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Show referral tree for a user (app user or admin user e.g. supervisor/lead)
     */
    public function referralTree(User $user)
    {
        if ($user->admin_role !== null) {
            // Admin user (e.g. supervisor/lead): require permission to manage admin users
            if (!request()->user()->canManageAdminUsers()) {
                abort(403, 'You do not have permission to view this tree.');
            }
            if (request()->user()->isInvitedAdmin() && !$user->joined_via_invite) {
                abort(403, 'You can only view trees of invited admin users.');
            }
        } else {
            $this->ensureInvitedAdminCanAccess($user);
        }

        $tree = $this->buildReferralTree($user->id);
        
        return view('admin.users.referral-tree', [
            'rootUser' => $user,
            'tree' => $tree,
        ]);
    }

    /**
     * Build referral tree recursively
     */
    private function buildReferralTree($userId, &$visited = [], $level = 0, $maxLevel = 10)
    {
        // Prevent infinite loops and limit depth
        if (in_array($userId, $visited) || $level >= $maxLevel) {
            return null;
        }

        $visited[] = $userId;
        
        $user = User::with(['inviteTokens.usedBy'])->find($userId);
        
        if (!$user) {
            return null;
        }

        $node = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_no' => $user->phone_no,
            'joined_via_invite' => $user->joined_via_invite,
            'created_at' => $user->created_at->toIso8601String(),
            'level' => $level,
            'children' => [],
        ];

        // Get all users who used this user's invite tokens
        $usedTokens = $user->inviteTokens()->where('is_used', true)->with('usedBy')->get();
        
        foreach ($usedTokens as $token) {
            if ($token->usedBy && !in_array($token->usedBy->id, $visited)) {
                $childNode = $this->buildReferralTree($token->usedBy->id, $visited, $level + 1, $maxLevel);
                if ($childNode) {
                    $node['children'][] = $childNode;
                }
            }
        }

        return $node;
    }
}
