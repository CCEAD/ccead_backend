<?php

namespace App\Http\Resources\Caja;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;

class CajaAdminCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function($caja) {

            switch ($caja->estado) {
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

            if ($caja->estado == 1) {
                $cubi = collect($caja->ingresos)->transform(function($ingreso) {
                    return [
                        'active' => $ingreso->pivot->active,
                        'codigo' => $ingreso->pivot->ubigeo->codigo,
                    ];
                })->first(function ($value, $key) {
                    return $value['active'] == 1;
                });
            } else {
                $cubi = 'N/A';
            }

            return [
                'id' => $caja->id,
                'gestion' => $caja->gestion,
                'cod_interno' => $caja->cod_interno,
                'cod_almacen' => substr($caja->cod_almacen, 0, 8),
                'observaciones' => $caja->observaciones,
                'estado' => $estado,
                'ubigeo' => $cubi,
                'created_at' => Carbon::parse($caja->created_at)->format('d/m/Y'),
                'updated_at' => Carbon::parse($caja->updated_at)->format('d/m/Y'),
            ];
        });
    }
}
