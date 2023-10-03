<?php

namespace App\Http\Resources\Permiso;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;

class PermisoCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function($permission) {
            return [
                'id' => $permission->id,
                'modulo' => strtok($permission->name, '.'),
                'acceso' => explode('.', $permission->name)[1],
                'created_at' => Carbon::parse($permission->created_at)->format('d/m/Y'),
                'updated_at' => Carbon::parse($permission->updated_at)->format('d/m/Y'),
            ];
        })->groupBy('modulo');
    }
}
