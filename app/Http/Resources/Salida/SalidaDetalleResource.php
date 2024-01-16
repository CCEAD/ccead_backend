<?php

namespace App\Http\Resources\Salida;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Carpeta\CarpetaAdminResource;
use App\Http\Resources\Reingreso\ReingresoResource;
use Carbon\Carbon;

class SalidaDetalleResource extends JsonResource
{
    public function toArray($request)
    {
        switch ($this->estado) {
            case 0:
                $estado = "Pendiente";
                break;
            case 1:
                $estado = "Aprobado";
                break;
            case 2:
                $estado = "Entregado";
                break;
            case 3:
                $estado = "Rechazado";
                break;
            default:
                $estado = "Rechazado";
        };

        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'fecha_solicitud' => Carbon::parse($this->fecha_solicitud)->format('d/m/Y'),
            'fecha_aprobacion' => $this->fecha_aprobacion ? Carbon::parse($this->fecha_aprobacion)->format('d/m/Y') : '(Sin Fecha)',
            'fecha_entrega' => $this->fecha_entrega ? Carbon::parse($this->fecha_entrega)->format('d/m/Y') : '(Sin Fecha)',
            'observacion' => $this->observacion,
            'estado' => $estado,
            'ingresado' => $this->ingresado == 0 ? false : true,
            'cajas' => collect($this->cajas)->transform(function($caja){
                $cubi = collect($caja->detalle_ingreso()->where('ingreso_id', $caja->ingresos->first()->id)->get())->first()->codigo;
                return [
                    'id' => $caja->id,
                    'gestion' => $caja->gestion,
                    'cod_interno' => $caja->cod_interno,
                    'cubi' => $cubi,
                    'carpetas_retiradas' => collect($caja->detalle_salida()->where('salida_id', $this->id)->get())->transform(function($detalle){
                        return new CarpetaAdminResource($detalle->carpetas);
                    }),
                    // 'carpetas_nuevas' => collect($caja->carpetas()->where('estado', 0)->get())->transform(function($carpeta){
                    //     return new CarpetaAdminResource($carpeta);
                    // })
                ];
            }),
            // 'reingreso' => $this->reingreso ? new ReingresoResource($this->reingreso) : NULL
        ];
    }
}
