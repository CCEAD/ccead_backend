<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;

class UserAdminCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function($usuario) {

            switch ($usuario->estado) {
                case 0:
                    $estado = "Inactivo";
                    break;
                case 1:
                    $estado = "Activo";
                    break;
                default:
                    $estado = "Inactivo";
            };

            return [
                'id' => $usuario->id,
                'nombres' => $usuario->nombres,
                'apellidos' => $usuario->apellidos,
                'usuario' => $usuario->name,
                'correo' => $usuario->email,
                'telefono' => $usuario->telefono,
                'estado' => $estado,
                'agencia' => $usuario->agencia->razon_social,
                'perfil' => $usuario->roles[0]->name,
                'created_at' => Carbon::parse($usuario->created_at)->format('d/m/Y'),
                'updated_at' => Carbon::parse($usuario->updated_at)->format('d/m/Y'),
            ];
        });
    }
}
