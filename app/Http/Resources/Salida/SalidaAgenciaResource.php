<?php

namespace App\Http\Resources\Salida;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class SalidaAgenciaResource extends JsonResource
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
            'fecha_solicitud' => $this->fecha_solicitud,
            'fecha_aprobacion' => $this->fecha_aprobacion,
            'fecha_entrega' =>$this->fecha_entrega,
            'observacion' => $this->observacion,
            'estado' => $estado,
            'total_cajas' => $this->cajas()->count(),
            // 'total_carpetas' => $this->carpetas()->count(),
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d/m/Y'),
        ];
    }
}
