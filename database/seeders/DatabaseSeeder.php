<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(RolesSeeder::class);
        $this->call(AdminUserSeeder::class);
        $this->call(PermisosSeeder::class);
        $this->call(RolesHasPermisosSeeder::class);
    }
}
