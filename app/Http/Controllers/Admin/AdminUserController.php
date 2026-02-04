<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereNotNull('admin_role')
            ->with('createdBy', 'permissions')
            ->orderByRaw("CASE admin_role WHEN 'super_admin' THEN 1 WHEN 'lead' THEN 2 WHEN 'supervisor' THEN 3 ELSE 4 END")
            ->orderBy('name');

        // Invited lead/supervisor: only see invited admin users (joined_via_invite)
        if (Auth::user()->isInvitedAdmin()) {
            $query->where('joined_via_invite', true);
        }

        $users = $query->paginate(15);
        return view('admin.admin-users.index', compact('users'));
    }

    public function create()
    {
        $current = Auth::user();
        $canCreateLead = $current->isSuperAdmin();
        $canCreateSupervisor = $current->isSuperAdmin() || $current->isLead();
        // Lead can only assign permissions they have; super admin sees all
        $permissions = $current->isSuperAdmin()
            ? Permission::orderBy('sort_order')->get()
            : $current->permissions()->orderBy('permissions.sort_order')->get();
        $showInvitedCheckbox = $current->isSuperAdmin();
        $forceInvitedSupervisor = $current->isInvitedAdmin() && $current->isLead();
        return view('admin.admin-users.create', compact('canCreateLead', 'canCreateSupervisor', 'permissions', 'showInvitedCheckbox', 'forceInvitedSupervisor'));
    }

    public function store(Request $request)
    {
        $current = Auth::user();
        $canCreateLead = $current->isSuperAdmin();
        $canCreateSupervisor = $current->isSuperAdmin() || $current->isLead();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'admin_role' => ['required', Rule::in(array_filter([
                $canCreateLead ? 'lead' : null,
                $canCreateSupervisor ? 'supervisor' : null,
            ]))],
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];
        if ($current->isSuperAdmin()) {
            $rules['is_invited_admin'] = 'nullable|boolean';
        }
        $request->validate($rules);

        $joinedViaInvite = false;
        if ($current->isInvitedAdmin() && $current->isLead()) {
            $joinedViaInvite = true; // invited lead can only add invited supervisors
        } elseif ($current->isSuperAdmin() && $request->has('is_invited_admin')) {
            $joinedViaInvite = (bool) $request->is_invited_admin;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'admin_role' => $request->admin_role,
            'created_by_admin_id' => $current->id,
            'joined_via_invite' => $joinedViaInvite,
        ]);

        // Super admin: assign any permissions. Lead: only assign permissions the lead has.
        if ($request->has('permissions') && is_array($request->permissions)) {
            $requestedIds = array_map('intval', $request->permissions);
            if ($current->isSuperAdmin()) {
                $user->permissions()->sync($requestedIds);
            } elseif ($current->isLead()) {
                $allowedIds = $current->permissions->pluck('id')->map(fn($id) => (int) $id)->toArray();
                $validIds = array_values(array_intersect($requestedIds, $allowedIds));
                $user->permissions()->sync($validIds);
            }
        }

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin user created successfully.');
    }

    public function edit(User $admin_user)
    {
        if ($admin_user->admin_role === null) {
            abort(404);
        }
        // Invited lead/supervisor can only edit invited admin users
        if (Auth::user()->isInvitedAdmin() && !$admin_user->joined_via_invite) {
            abort(403, 'You can only edit invited admin users.');
        }
        $current = Auth::user();
        // Lead can only assign permissions they have; super admin sees all
        $permissions = $current->isSuperAdmin()
            ? Permission::orderBy('sort_order')->get()
            : $current->permissions()->orderBy('permissions.sort_order')->get();
        $user = $admin_user;
        $showInvitedCheckbox = $current->isSuperAdmin();
        return view('admin.admin-users.edit', compact('user', 'permissions', 'showInvitedCheckbox'));
    }

    public function update(Request $request, User $admin_user)
    {
        if ($admin_user->admin_role === null) {
            abort(404);
        }
        if (Auth::user()->isInvitedAdmin() && !$admin_user->joined_via_invite) {
            abort(403, 'You can only edit invited admin users.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin_user->id,
        ];
        if ($request->filled('password')) {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }
        $rules['permissions'] = 'nullable|array';
        $rules['permissions.*'] = 'exists:permissions,id';
        if (Auth::user()->isSuperAdmin()) {
            $rules['is_invited_admin'] = 'nullable|boolean';
        }
        $request->validate($rules);

        $data = ['name' => $request->name, 'email' => $request->email];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        if (Auth::user()->isSuperAdmin() && $request->has('is_invited_admin')) {
            $data['joined_via_invite'] = (bool) $request->is_invited_admin;
        }
        $admin_user->update($data);

        // Super admin: assign any permissions. Lead: only assign permissions the lead has.
        if ($request->has('permissions') && is_array($request->permissions)) {
            $requestedIds = array_map('intval', $request->permissions);
            $current = Auth::user();
            if ($current->isSuperAdmin()) {
                $admin_user->permissions()->sync($requestedIds);
            } elseif ($current->isLead()) {
                $allowedIds = $current->permissions->pluck('id')->map(fn($id) => (int) $id)->toArray();
                $validIds = array_values(array_intersect($requestedIds, $allowedIds));
                $admin_user->permissions()->sync($validIds);
            }
        }

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin user updated successfully.');
    }

    public function destroy(User $admin_user)
    {
        if ($admin_user->admin_role === null || $admin_user->isSuperAdmin()) {
            abort(403, 'Cannot delete this user.');
        }
        if (Auth::user()->isInvitedAdmin() && !$admin_user->joined_via_invite) {
            abort(403, 'You can only remove invited admin users.');
        }
        $admin_user->update(['admin_role' => null, 'created_by_admin_id' => null, 'joined_via_invite' => false]);
        $admin_user->permissions()->detach();
        return redirect()->route('admin.admin-users.index')->with('success', 'Admin user removed successfully.');
    }
}
