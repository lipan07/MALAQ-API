<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'All Posts', 'slug' => 'posts', 'description' => 'View and manage posts', 'icon' => 'bi-file-post', 'sort_order' => 10],
            ['name' => 'Categories', 'slug' => 'categories', 'description' => 'View and manage categories', 'icon' => 'bi-tags', 'sort_order' => 20],
            ['name' => 'Users', 'slug' => 'users', 'description' => 'View and manage app users', 'icon' => 'bi-people', 'sort_order' => 30],
            ['name' => 'Payments', 'slug' => 'payments', 'description' => 'View and confirm payments', 'icon' => 'bi-credit-card', 'sort_order' => 40],
            ['name' => 'All Invite Tokens', 'slug' => 'all_invite_tokens', 'description' => 'View all invite tokens with expiry countdown', 'icon' => 'bi-gift', 'sort_order' => 45],
            ['name' => 'Admin Users', 'slug' => 'admin_users', 'description' => 'Create and manage admin users (lead/supervisor)', 'icon' => 'bi-person-gear', 'sort_order' => 50],
            ['name' => 'Roles & Permissions', 'slug' => 'roles_permissions', 'description' => 'Assign permissions to admin users', 'icon' => 'bi-shield-lock', 'sort_order' => 60],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['slug' => $p['slug']], $p);
        }
    }
}
