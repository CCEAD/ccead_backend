<?php

namespace App\Http\Resources\Caja;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CajaEditResource extends JsonResource
{
    public function toArray($request)
    {
        switch ($this->estado) {
            case 0:
                $estado = "PENDIENTE";
                break;
            case 1:
                $estado = "EN ALMACÉN";
                break;
            case 2:
                $estado = "RETIRADA";
                break;
            default:
                $estado = "RETIRADA";
        };

        return [
            'id' => $this->id,
            'gestion' => $this->gestion,
            'cod_interno' => $this->cod_interno,
            'cod_almacen' => $this->cod_almacen,
            'cant_carpetas' => $this->cant_carpetas,
            'reg_inicial' => $this->reg_inicial,
            'reg_final' => $this->reg_final,
            'observaciones' => $this->observaciones,
            'estado' => $estado,
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d/m/Y'),
        ];
    }
}
