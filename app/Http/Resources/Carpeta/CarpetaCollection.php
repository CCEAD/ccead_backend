<?php

namespace App\Http\Resources\Carpeta;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Carpeta\CarpetaResource;

class CarpetaCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function($carpeta){
            return new CarpetaResource($carpeta);
        });
    }
}
