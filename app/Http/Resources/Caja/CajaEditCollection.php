<?php

namespace App\Http\Resources\Caja;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Caja\CajaEditResource;

class CajaEditCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function($caja){
            return new CajaEditResource($caja);
        });
    }
}
