<?php

namespace App\Http\Resources\Ubigeo;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Ubigeo\UbigeoResource;

class UbigeoCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function($ubigeo){
            return new UbigeoResource($ubigeo);
        });
    }
}
