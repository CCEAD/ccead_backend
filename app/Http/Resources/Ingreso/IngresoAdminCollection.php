<?php

namespace App\Http\Resources\Ingreso;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Carpeta\CarpetaAdminResource;
use Carbon\Carbon;

class IngresoAdminCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function($ingreso) {

            switch ($ingreso->estado) {
                case 0:
                    $estado = "Pendiente";
                    break;
                case 1:
                    $estado = "Aprobado";
                    break;
                case 2:
                    $estado = "Entregado";
                    break;
                case 3:
                    $estado = "Rechazado";
                    break;
                default:
                    $estado = "Rechazado";
            };

            return [
                'id' => $ingreso->id,
                'codigo' => $ingreso->codigo,
                'fecha_solicitud' => $ingreso->fecha_solicitud,
                'fecha_aprobacion' => $ingreso->fecha_aprobacion,
                'fecha_entrega' => $ingreso->fecha_entrega,
                'observacion' => $ingreso->observacion,
                // 'total_cajas' => $ingreso->cajas()->count(),
                // 'total_carpetas' => $ingreso->carpetas()->count(),
                // 'cajas' => collect($ingreso->cajas)->transform(function($caja) use ($ingreso){
                //     if ($ingreso->estado == 2) {
                //         $cubi = collect($caja->detalle_ingreso()->where('ingreso_id', $ingreso->id)->get())->first()->codigo;
                //     } else {
                //         $cubi = null;
                //     }
                //     return [
                //         'id' => $caja->id,
                //         'gestion' => $caja->gestion,
                //         'cod_interno' => $caja->cod_interno,
                //         'cubi' => $cubi,
                //         'carpetas' => collect($caja->carpetas)->transform(function($carpeta){
                //             return new CarpetaAdminResource($carpeta);
                //         })
                //    ];
                // }),
                'estado' => $estado,
                'created_at' => Carbon::parse($ingreso->created_at)->format('d/m/Y'),
                'updated_at' => Carbon::parse($ingreso->updated_at)->format('d/m/Y'),
            ];
        });
    }
}
