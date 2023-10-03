<?php

namespace App\Http\Resources\Salida;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Carpeta\CarpetaAdminResource;

class SalidaReingresoResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'cajas' => collect($this->cajas)->transform(function($caja){
                return [
                    'id' => $caja->id,
                    'gestion' => $caja->gestion,
                    'cod_interno' => $caja->cod_interno,
                    'cod_almacen' => substr($caja->cod_almacen, 0, 8),
                    'carpetas' => [
                        "antiguas" => collect($caja->detalle_salida()->where('salida_id', $this->id)->get())->transform(function($detalle){
                            return new CarpetaAdminResource($detalle->carpetas);
                        }), 
                        "nuevas" => collect($caja->carpetas()->where(function ($query) {
                            $query->where('estado', 0)
                                  ->orWhere('estado', 3);
                        })->get())->transform(function($carpeta){
                            return new CarpetaAdminResource($carpeta);
                        }),
                    ]
                ];
            })
        ];
    }
}
