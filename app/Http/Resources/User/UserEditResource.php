<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UserEditResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'name' => $this->name,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'agencia_id' => $this->agencia_id,
            'rol_id' => $this->roles[0]->id,
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d/m/Y'),
        ];
    }
}
