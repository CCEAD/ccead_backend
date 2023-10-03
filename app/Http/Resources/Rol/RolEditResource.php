<?php

namespace App\Http\Resources\Rol;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class RolEditResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nombre' => $this->name,
            'permisos' => $this->permissions->map(function ($item, $key) {
                return $item->id;
            }),
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d/m/Y'),
        ];
    }
}
