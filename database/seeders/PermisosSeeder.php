<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Constants\Resource;
use Spatie\Permission\Models\Permission;

class PermisosSeeder extends Seeder
{
    public function run()
    {
        foreach (Resource::supported() as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }
    }
}
