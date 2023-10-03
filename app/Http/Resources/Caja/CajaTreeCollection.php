<?php

namespace App\Http\Resources\Caja;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CajaTreeCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function($caja){
            return new CajaResource($caja);
        })->groupBy('year');
    }
}
