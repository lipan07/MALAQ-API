<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('app.super_admin_email');
        $password = Str::password(16);

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'email' => $email,
                'password' => Hash::make($password),
                'admin_role' => 'super_admin',
                'created_by_admin_id' => null,
            ]
        );

        if ($this->command) {
            $this->command->info('Super Admin created. Login: ' . $email);
            $this->command->warn('Password (save this): ' . $password);
        }
    }
}
