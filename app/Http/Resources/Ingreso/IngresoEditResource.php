<?php

namespace App\Http\Resources\Ingreso;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Caja\CajaEditResource;

class IngresoEditResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'fecha_solicitud' => $this->fecha_solicitud,
            'observacion' => $this->observacion,
            'cajas' => collect($this->cajas)->transform(function($caja){
                return new CajaEditResource($caja);
            }),
        ];
    }
}
