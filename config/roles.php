<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin role hierarchy and default permissions
    |--------------------------------------------------------------------------
    | super_admin: full access (bypass in code)
    | admin: Operations Manager - can assign moderator, support, analyst
    | moderator: Content & Safety - approve listings, handle reports, no delete users/categories
    | support: Customer Support - view only + tickets + flag, no block/approve/delete
    | analyst: Read-only - dashboards, stats, export
    | lead / supervisor: legacy invited roles (lead can assign supervisor)
    */

    'all_roles' => [
        'super_admin'  => 'Super Admin',
        'admin'        => 'Admin (Operations Manager)',
        'moderator'    => 'Moderator (Content & Safety)',
        'support'      => 'Support Agent',
        'analyst'      => 'Analyst (Read-Only)',
        'lead'         => 'Lead (Invited)',
        'supervisor'   => 'Supervisor (Invited)',
    ],

    /** Roles that can be assigned by each role (key = assigner role) */
    'assignable_roles' => [
        'super_admin' => ['admin', 'moderator', 'support', 'analyst', 'lead', 'supervisor'],
        'admin'       => ['moderator', 'support', 'analyst'],
        'lead'        => ['supervisor'],
    ],

    /** Default permission slugs for each role (used when creating non-invited staff) */
    'default_permissions' => [
        'admin'     => ['users', 'posts', 'categories', 'reports', 'analytics', 'admin_users', 'payments', 'all_invite_tokens'],
        'moderator' => ['posts', 'reports'],
        'support'   => ['users', 'posts', 'support_tickets'],
        'analyst'   => ['analytics'],
        'lead'      => [], // assigned manually
        'supervisor'=> [], // assigned by lead/super_admin
    ],
];
