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
            ->orderByRaw("CASE admin_role WHEN 'super_admin' THEN 1 WHEN 'admin' THEN 2 WHEN 'lead' THEN 3 WHEN 'moderator' THEN 4 WHEN 'support' THEN 5 WHEN 'analyst' THEN 6 WHEN 'supervisor' THEN 7 ELSE 8 END")
            ->orderBy('name');

        // Invited lead/supervisor: only see invited admin users (joined_via_invite)
        if (Auth::user()->isInvitedAdmin()) {
            $query->where('joined_via_invite', true);
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 15;
        $users = $query->paginate($perPage);
        return view('admin.admin-users.index', compact('users', 'perPage'));
    }

    public function create()
    {
        $current = Auth::user();
        $assignableRoles = $current->rolesAssignableByCurrentUser();
        if (empty($assignableRoles)) {
            abort(403, 'You cannot create admin users.');
        }
        // Permissions: for lead/supervisor creation; super_admin sees all, lead only their permissions
        $permissions = $current->isSuperAdmin()
            ? Permission::orderBy('sort_order')->get()
            : $current->permissions()->orderBy('permissions.sort_order')->get();
        $showInvitedCheckbox = $current->isSuperAdmin();
        $forceInvitedSupervisor = $current->isInvitedAdmin() && $current->isLead();
        return view('admin.admin-users.create', compact('assignableRoles', 'permissions', 'showInvitedCheckbox', 'forceInvitedSupervisor'));
    }

    public function store(Request $request)
    {
        $current = Auth::user();
        $assignableRoles = $current->rolesAssignableByCurrentUser();
        $allowedRoleKeys = array_keys($assignableRoles);
        if (empty($allowedRoleKeys)) {
            abort(403, 'You cannot create admin users.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'admin_role' => ['required', Rule::in($allowedRoleKeys)],
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];
        if ($current->isSuperAdmin()) {
            $rules['is_invited_admin'] = 'nullable|boolean';
        }
        $request->validate($rules);

        $role = $request->admin_role;
        $staffRoles = ['admin', 'moderator', 'support', 'analyst'];
        $joinedViaInvite = false;
        if (in_array($role, $staffRoles, true)) {
            // Non-invited staff: no joined_via_invite
            $joinedViaInvite = false;
        } elseif ($current->isInvitedAdmin() && $current->isLead()) {
            $joinedViaInvite = true;
        } elseif ($current->isSuperAdmin() && $request->has('is_invited_admin')) {
            $joinedViaInvite = (bool) $request->is_invited_admin;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'admin_role' => $role,
            'created_by_admin_id' => $current->id,
            'joined_via_invite' => $joinedViaInvite,
        ]);

        if (in_array($role, $staffRoles, true)) {
            $slugs = User::defaultPermissionSlugsForRole($role);
            $permissionIds = Permission::whereIn('slug', $slugs)->pluck('id')->toArray();
            $user->permissions()->sync($permissionIds);
        } elseif ($request->has('permissions') && is_array($request->permissions)) {
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
        $permissions = $current->isSuperAdmin()
            ? Permission::orderBy('sort_order')->get()
            : $current->permissions()->orderBy('permissions.sort_order')->get();
        $user = $admin_user;
        $showInvitedCheckbox = $current->isSuperAdmin() && in_array($admin_user->admin_role, ['lead', 'supervisor'], true);
        $roleIsStaff = in_array($admin_user->admin_role, ['admin', 'moderator', 'support', 'analyst'], true);
        return view('admin.admin-users.edit', compact('user', 'permissions', 'showInvitedCheckbox', 'roleIsStaff'));
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

        $current = Auth::user();
        if ($request->has('permissions') && is_array($request->permissions)) {
            $requestedIds = array_map('intval', $request->permissions);
            if ($current->isSuperAdmin()) {
                $admin_user->permissions()->sync($requestedIds);
            } elseif ($current->isLead()) {
                $allowedIds = $current->permissions->pluck('id')->map(fn($id) => (int) $id)->toArray();
                $validIds = array_values(array_intersect($requestedIds, $allowedIds));
                $admin_user->permissions()->sync($validIds);
            } elseif ($current->isOperationsAdmin() && in_array($admin_user->admin_role, ['admin', 'moderator', 'support', 'analyst'], true)) {
                $admin_user->permissions()->sync($requestedIds);
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
