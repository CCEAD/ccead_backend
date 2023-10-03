<?php

namespace App\Http\Resources\Salida;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class SalidaResource extends JsonResource
{
    public function toArray($request)
    {
        switch ($this->estado) {
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

        // $cajas = collect($this->carpetas)->transform(function($carpeta){
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
        //      $detalle_cajas[$key] = [
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
            'id' => $this->id,
            'codigo' => $this->codigo,
            'fecha_solicitud' => $this->fecha_solicitud,
            'fecha_aprobacion' => $this->fecha_aprobacion,
            'fecha_entrega' => $this->fecha_entrega,
            'observacion' => $this->observacion,
            'total_cajas' => $this->cajas()->count(),
            // 'total_carpetas' => $this->carpetas()->count(),
            'ingresado' => $this->ingresado == 1 ? true : false,
            // 'cajas' => collect($this->cajas)->transform(function($caja){
            //     return [
            //         'id' => $caja->id,
            //         'gestion' => $caja->gestion,
            //         'carpetas' => collect($caja->detalle_salida()->where('salida_id', $this->id)->get())->transform(function($detalle){
            //             return $detalle->carpetas;
            //         })
            //     ];
            // }),
            // 'cajas' => collect($this->cajas)->transform(function($caja){
            //     return [
            //         'id' => $caja->id,
            //         'gestion' => $caja->gestion,
            //         'carpetas' => $caja->carpetas
            //     ];
            // }),
            'estado' => $estado,
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d/m/Y'),
        ];
    }
}
