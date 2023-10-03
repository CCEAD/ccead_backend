<?php

namespace App\Http\Resources\Salida;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Salida\SalidaResource;

class SalidaCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function($salida){
            return new SalidaResource($salida);
        });
    }
}
