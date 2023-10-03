<?php

namespace App\Http\Resources\Ingreso;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Ingreso\IngresoResource;

class IngresoCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function($ingreso){
            return new IngresoResource($ingreso);
        });
    }
}
