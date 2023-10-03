<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $roles = ['super_admin','admin', 'user'];

        foreach ($roles as $role) {
            Role::create(['name' => $role, 'guard_name' => 'api']);
        }
    }
}
