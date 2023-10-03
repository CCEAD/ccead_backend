<?php

namespace App\Http\Resources\Ingreso;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Carpeta\CarpetaAdminResource;
use Carbon\Carbon;

class IngresoDetalleResource extends JsonResource
{
    public function toArray($request)
    {
        switch ($this->estado) {
            case 0:
                $estado = "PENDIENTE";
                break;
            case 1:
                $estado = "APROBADO";
                break;
            case 2:
                $estado = "ENTREGADO";
                break;
            case 3:
                $estado = "RECHAZADO";
                break;
            default:
                $estado = "RECHAZADO";
        };

        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'fecha_solicitud' => Carbon::parse($this->fecha_solicitud)->format('d/m/Y'),
            'fecha_aprobacion' => Carbon::parse($this->fecha_aprobacion)->format('d/m/Y'),
            'fecha_entrega' => Carbon::parse($this->fecha_entrega)->format('d/m/Y'),
            'observacion' => $this->observacion,
            'estado' => $estado,
            'cajas' => collect($this->cajas)->transform(function($caja) {
                if ($this->estado == 2) {
                    $cubi = collect($caja->detalle_ingreso()->where('ingreso_id', $this->id)->get())->first()->codigo;
                } else {
                    $cubi = null;
                }
                return [
                    'id' => $caja->id,
                    'gestion' => $caja->gestion,
                    'cod_interno' => $caja->cod_interno,
                    'cubi' => $cubi,
                    'carpetas' => collect($caja->carpetas)->transform(function($carpeta){
                        return new CarpetaAdminResource($carpeta);
                    })
               ];
            }),
        ];
    }
}
