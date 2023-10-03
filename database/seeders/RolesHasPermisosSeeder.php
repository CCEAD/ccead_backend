<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Constants\Resource;

class RolesHasPermisosSeeder extends Seeder
{
    public function run()
    {
        $this->createAdminRolePermissions();
        $this->createUserRolePermissions();
    }

    private function createAdminRolePermissions()
    {
        $role = Role::findByName('admin', 'api');
        $role->syncPermissions(Permission::all());
    }

    private function createUserRolePermissions()
    {
        $role = Role::findByName('user', 'api');
        $role->syncPermissions(Permission::where('name', Resource::UBIGEO_INDEX)->first());
    }
}
