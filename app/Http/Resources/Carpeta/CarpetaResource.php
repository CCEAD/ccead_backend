<?php

namespace App\Http\Resources\Carpeta;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CarpetaResource extends JsonResource
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
            'gestion' => $this->caja->gestion,
            'nro_declaracion' => $this->nro_declaracion,
            'nro_registro' => $this->nro_registro,
            'fecha_aceptacion' => Carbon::parse($this->fecha_aceptacion)->format('d/m/Y H:i'),
            'aduana_tramite' =>  $this->aduana ? $this->aduana->text : 'N/A',
            'aduana_codigo' =>  $this->aduana ? $this->aduana->codigo : 'N/A',
            'regimen_aduanero' => $this->regimen_aduanero,
            'modalidad_regimen' => $this->modalidad_regimen,
            'modalidad_despacho' => $this->modalidad_despacho,
            'importador' => $this->importador,
            'declarante' => $this->declarante,
            'pais_exportacion' => $this->pais_exportacion,
            'aduana_ingreso' =>  $this->aduana_ingreso,
            'total_nro_facturas' => $this->total_nro_facturas,
            'total_nro_items' => $this->total_nro_items,
            'total_nro_bultos' => $this->total_nro_bultos,
            'total_peso_bruto' => $this->total_peso_bruto,
            'total_valor_fob' => $this->total_valor_fob,
            'estado' => $estado,
            'cantidad_documentos' => $this->cantidad_documentos,
            'digitalizado' => $this->digitalizado == 1 ? true : false,
            'caja' => $this->caja->cod_interno,
            'aduana_id' => $this->aduana_id,
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d/m/Y'),
        ];
    }
}
