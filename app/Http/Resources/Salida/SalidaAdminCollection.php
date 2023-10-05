<?php

namespace App\Http\Resources\Salida;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Carpeta\CarpetaAdminResource;
use Carbon\Carbon;

class SalidaAdminCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function($salida) {

            switch ($salida->estado) {
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

            // $cajas = collect($salida->carpetas)->transform(function($carpeta){
            //     return [
            //         'id' => $carpeta->id,
            //         'codigo' => $carpeta->codigo,
            //         'nro_declaracion' => $carpeta->nro_declaracion,
            //         'caja' => [
            //             'id' => $carpeta->caja->id,
            //             'gestion' => $carpeta->caja->gestion,
            //             'cod_interno' => $carpeta->caja->cod_interno,
            //             'cod_almacen' => $carpeta->caja->cod_almacen
            //         ],
            //     ];
            // })->groupBy('caja.id')->all();

            // $detalle_cajas = [];
            // foreach ($cajas as $key => $value) {
            //     $obj = collect($value)->first();
            //     $detalle_cajas[$key] = [
            //         'id' => $obj['caja']['id'],
            //         'gestion' => $obj['caja']['gestion'],
            //         'cod_interno' => $obj['caja']['cod_interno'],
            //         'carpetas' => collect($value)->transform(function($carpeta){
            //             return [
            //                 'id' => $carpeta['id'],
            //                 'codigo' => $carpeta['codigo'],
            //                 'nro_declaracion' => $carpeta['nro_declaracion'],
            //             ];
            //         })
            //     ];
            // }

            return [
                'id' => $salida->id,
                'codigo' => $salida->codigo,
                'fecha_solicitud' => $salida->fecha_solicitud,
                'fecha_aprobacion' => $salida->fecha_aprobacion,
                'fecha_entrega' => $salida->fecha_entrega,    
                'observacion' => $salida->observacion,
                'total_cajas' => $salida->cajas()->count(),
                'total_carpetas' => $salida->carpetas()->count(),
                'ingresado' => $salida->ingresado == 1 ? true : false,
                // 'cajas' => collect($salida->cajas)->transform(function($caja) use ($salida) {
                //     return [
                //         'id' => $caja->id,
                //         'gestion' => $caja->gestion,
                //         'carpetas' => collect($caja->detalle_salida()->where('salida_id', $salida->id)->get())->transform(function($detalle){
                //             return new CarpetaAdminResource($detalle->carpetas);
                //         })
                //     ];
                // }),
                'estado' => $estado,
                'created_at' => Carbon::parse($salida->created_at)->format('d/m/Y'),
                'updated_at' => Carbon::parse($salida->updated_at)->format('d/m/Y'),
            ];
        });
    }
}
