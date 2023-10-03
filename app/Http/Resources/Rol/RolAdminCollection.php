<?php

namespace App\Http\Resources\Rol;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;

class RolAdminCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function($rol) {
            return [
                'id' => $rol->id,
                'nombre' => $rol->name,
                'agencia' => $rol->agencia->razon_social,
                'permisos' => $rol->permissions->transform(function($permission) {
                    return [
                        'id' => $permission->id,
                        'modulo' => strtok($permission->name, '.'),
                        'acceso' => explode('.', $permission->name)[1],
                        'created_at' => Carbon::parse($permission->created_at)->format('d/m/Y'),
                        'updated_at' => Carbon::parse($permission->updated_at)->format('d/m/Y'),
                    ];
                })->groupBy('modulo'),
                'created_at' => Carbon::parse($rol->created_at)->format('d/m/Y'),
                'updated_at' => Carbon::parse($rol->updated_at)->format('d/m/Y'),
            ];
        });
    }
}
