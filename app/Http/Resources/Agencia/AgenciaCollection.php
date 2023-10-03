<?php

namespace App\Http\Resources\Agencia;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Agencia\AgenciaResource;

class AgenciaCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function($agencia){
            return new AgenciaResource($agencia);
        });
    }
}
