<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /** Predefined roles - view only, no create/edit. */
    public function index()
    {
        $roles = [
            ['key' => 'super_admin', 'name' => 'Super Admin', 'description' => 'Full access. Can create lead users and assign all permissions. Only one super admin exists.'],
            ['key' => 'lead', 'name' => 'Lead', 'description' => 'Created by super admin. Can create supervisor users.'],
            ['key' => 'supervisor', 'name' => 'Supervisor', 'description' => 'Created by super admin or lead. Access based on assigned permissions.'],
        ];
        return view('admin.roles.index', compact('roles'));
    }
}
