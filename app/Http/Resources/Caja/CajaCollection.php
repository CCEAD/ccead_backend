<?php

namespace App\Http\Resources\Caja;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Caja\CajaResource;

class CajaCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function($caja){
            return new CajaResource($caja);
        });
    }
}
