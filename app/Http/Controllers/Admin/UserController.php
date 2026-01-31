<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('inviteTokens');

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

        $users = $users->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('inviteTokens.usedBy');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
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
        $user->update(['status' => 'blocked']);

        return back()->with('success', 'User has been blocked successfully.');
    }

    public function unblock(User $user)
    {
        $user->update(['status' => 'online']);

        return back()->with('success', 'User has been unblocked successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Show referral tree for a user
     */
    public function referralTree(User $user)
    {
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
