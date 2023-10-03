<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Rol;
use App\Http\Controllers\Api\V1\ApiController;
use Illuminate\Http\Request;
use App\Http\Requests\Rol\RolRequest;
use Spatie\Permission\Models\Role;
use App\Http\Resources\Rol\RolEditResource;
use App\Http\Resources\Rol\RolAdminCollection;
use App\Http\Resources\Rol\RolAgenciaCollection;
use Illuminate\Support\Facades\DB;

class RoleController extends ApiController
{
    private $rol;

    public function __construct(Rol $rol)
    {
        $this->rol = $rol;
    }

    public function rolesAdmin()
    {
        $roles = $this->rol->all();
        return new RolAdminCollection($roles);
    }

    public function rolesAgencia()
    {
        $roles =  $this->rol->where('team_id', getPermissionsTeamId())->get();
        return new RolAgenciaCollection($roles); 
    }

    public function store(RolRequest $request)
    {
        DB::beginTransaction();

        try {
            $rol = $this->rol->create([
                'name' => $request->nombre, 
                'guard_name' => 'api', 
                'team_id' => getPermissionsTeamId(), 
            ]);

            $rol->syncPermissions($request->permisos);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }

        return $this->respondCreated();
    }

    public function edit(Rol $rol)
    {
        return new RolEditResource($rol);
    }

    public function update(Request $request, Rol $rol)
    {
        DB::beginTransaction();

        try {
            $rol->name = $request->nombre;
            $rol->save();

            $rol->syncPermissions($request->permisos);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }

        return $this->respondUpdated();
    }

    public function destroy(Rol $rol)
    {
        try {
            $model = $rol->secureDelete();
            if ($model) {
                return $this->respond([
                    "success" => false,
                    "data" => null,
                    "message" => message('MSG015')
                ], 403);
            }
        } catch (Exception $e) {
            return $this->respondInternalError();
        }

        return $this->respondDeleted();
    }

    public function lista() 
    {
        $roles = $this->rol->listRoles(get_user_agencia());

        return $this->respond($roles);
    }
}
