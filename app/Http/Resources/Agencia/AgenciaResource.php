<?php

namespace App\Http\Resources\Agencia;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Representante\RepresentanteResource;
use Carbon\Carbon;

class AgenciaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'razon_social' => $this->razon_social,
            'nit' => $this->nit,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
            'poder_representacion' => asset('storage/pdf/documentos') .'/'. $this->poder_representacion,
            'matricula_comercio' => asset('storage/pdf/documentos') .'/'. $this->matricula_comercio,
            'licencia_funcionamiento' => asset('storage/pdf/documentos') .'/'. $this->licencia_funcionamiento,
            'estado' => $this->estado ? 'ACTIVO' : 'PENDIENTE',
            'representante' => new RepresentanteResource($this->representante),
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d/m/Y'),
        ];
    }
}
