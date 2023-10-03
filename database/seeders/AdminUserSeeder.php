<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{

    public function run()
    {
        $super_admin = User::create([
            'name' => 'nano',
            'email' => 'super_admin@superadmin.com',
            'password' => '12345',
        ]);

        $super_admin->assignRole('super_admin');

        $admin = User::create([
            'name' => 'fercho',
            'email' => 'admin@admin.com',
            'password' => '12345',
        ]);

        $admin->assignRole('admin');

        $user = User::create([
            'name' => 'mono',
            'email' => 'user@user.com',
            'password' => '12345',
        ]);

        $user->assignRole('user');
    }
}
