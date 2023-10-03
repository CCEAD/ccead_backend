<?php

namespace App\Http\Resources\Reingreso;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ReingresoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'fecha' => $this->fecha,
            'observacion' => $this->observacion,
            'estado' => $this->estado === 0 ? 'ANULADO' : 'RECIBIDO',
            'salida' => $this->salida->codigo,
            'total_cajas' => $this->cajas->count(),
            'cajas' => collect($this->cajas)->transform(function($caja) {
                
                $carpetas = collect($caja->detalle_reingreso()->where('reingreso_id', $this->id)->get())->transform(function($detalle){
                    return [
                        'tipo' => $detalle->type == 0 ? 'retiradas' : 'agregadas',
                        'carpetas' => $detalle->carpetas
                    ];
                })->groupBy('tipo');

                $data = [];

                foreach ($carpetas as $ki => $carpeta) {
                    $c = [];
                    foreach ($carpeta as $kj => $item) {
                        $c[$kj] = $item['carpetas'];
                    }

                    $data[$ki] = $c;
                }

                return [
                    'id' => $caja->id,
                    'gestion' => $caja->gestion,
                    'cod_interno' => $caja->cod_interno,
                    'cubi' => collect($caja->detalle_ingreso)->first()->codigo,
                    'cod_almacen' => substr($caja->cod_almacen, 0, 8),
                    'carpetas' => $data
                ];
            }),
        ];
    }
}
