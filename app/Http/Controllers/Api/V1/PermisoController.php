<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Permiso;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Permiso\PermisoCollection;
use Illuminate\Http\Request;

class PermisoController extends ApiController
{
    private $permiso;

    public function __construct(Permiso $permiso)
    {
        $this->permiso = $permiso;
    }

    public function lista() 
    {
        if (verificar_agencia()) {
            $permisos = $this->permiso->all();
        } else {
            $permisos = $this->permiso->all()->except([1,2,3,4,5,11,12]);
        }

        return new PermisoCollection($permisos);
    }
}
