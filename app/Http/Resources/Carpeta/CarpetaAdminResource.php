<?php

namespace App\Http\Resources\Carpeta;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CarpetaAdminResource extends JsonResource
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
            'codigo' => $this->codigo,
            'nro_declaracion' => $this->nro_declaracion,
            'nro_registro' => $this->nro_registro,
            'fecha_aceptacion' => Carbon::parse($this->fecha_aceptacion)->format('d/m/Y'),
            'estado' => $estado,
            'aduana_tramite' =>  $this->aduana ? $this->aduana->text : 'N/A',
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d/m/Y'),
        ];
    }
}
