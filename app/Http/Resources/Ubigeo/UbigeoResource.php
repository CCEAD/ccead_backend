<?php

namespace App\Http\Resources\Ubigeo;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UbigeoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'numero_interno' => $this->numero_interno,
            'codigo' => $this->codigo,
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d/m/Y'),
        ];
    }
}
