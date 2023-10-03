<?php

namespace App\Http\Resources\Carpeta;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CarpetaEditResource extends JsonResource
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
            'fecha_aceptacion' => Carbon::parse($this->fecha_aceptacion)->format('Y-m-d'),
            'aduana_tramite' => $this->aduana ? $this->aduana->text : 'N/A',
            'regimen_aduanero' => $this->regimen_aduanero,
            'modalidad_regimen' => $this->modalidad_regimen,
            'modalidad_despacho' => $this->modalidad_despacho,
            'importador' => $this->importador,
            'declarante' => $this->declarante,
            'pais_exportacion' => $this->pais_exportacion,
            'aduana_ingreso' =>  $this->aduana_ingreso,
            'cantidad_documentos' => $this->cantidad_documentos,
            'total_nro_facturas' => $this->total_nro_facturas,
            'total_nro_items' => $this->total_nro_items,
            'total_nro_bultos' => $this->total_nro_bultos,
            'total_peso_bruto' => $this->total_peso_bruto,
            'total_valor_fob' => $this->total_valor_fob,
            'estado' => $estado,
            'aduana_id' => $this->aduana_id,
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d/m/Y'),
        ];
    }
}
