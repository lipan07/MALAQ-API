<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /** Predefined roles - view only, no create/edit. */
    public function index()
    {
        $roles = [
            ['key' => 'super_admin', 'name' => 'Super Admin', 'description' => 'Full access. Can do anything and give any permission to anyone.'],
            ['key' => 'admin', 'name' => 'Admin (Operations Manager)', 'description' => 'Manage users (block/unblock), approve/reject listings, see categories, handle reports, send notifications, view analytics. Can assign Moderator, Support, Analyst. Cannot: change system settings, delete Super Admin, access financial configs.'],
            ['key' => 'moderator', 'name' => 'Moderator (Content & Safety)', 'description' => 'Review reported users/listings, approve/reject listings, disable listings, warn users. Cannot: delete users, edit categories, view financial or system data.'],
            ['key' => 'support', 'name' => 'Support Agent', 'description' => 'View users, view listings, view chats metadata, respond to support tickets, flag for review. Cannot: block users, delete listings, approve listings.'],
            ['key' => 'analyst', 'name' => 'Analyst (Read-Only)', 'description' => 'View dashboards, user stats, listing stats, export reports. Cannot change anything.'],
            ['key' => 'lead', 'name' => 'Lead (Invited)', 'description' => 'Created by super admin. Can create supervisor users (invited).'],
            ['key' => 'supervisor', 'name' => 'Supervisor (Invited)', 'description' => 'Created by super admin or lead. Access based on assigned permissions.'],
        ];
        return view('admin.roles.index', compact('roles'));
    }
}
