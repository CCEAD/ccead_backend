<?php

namespace App\Transformers;
use Carbon\Carbon;

class SalidaTransformer extends Transformer
{
    protected $resourceName = 'salida';

    public function transform($data)
    {
        switch ($data['estado']) {
            case 0:
                $estado = "PENDIENTE";
                break;
            case 1:
                $estado = "APROBADO";
                break;
            case 2:
                $estado = "ENTREGADO";
                break;
            case 3:
                $estado = "RECHAZADO";
                break;
            default:
                $estado = "RECHAZADO";
        };

        $numero = explode("-", $data['codigo'], 3);

        return [
            'id' => $data['id'],
            'numero' => $numero[1],
            'codigo' => $data['codigo'],
            'agencia' => $data['agencia']['razon_social'],
            'fecha_solicitud' => $data['fecha_solicitud'],
            'fecha_aprobacion' => is_null($data['fecha_aprobacion']) ? 'N/A' : Carbon::parse($data['fecha_aprobacion'])->format('d/m/Y'),
            'fecha_entrega' => is_null($data['fecha_entrega']) ? 'N/A' : Carbon::parse($data['fecha_entrega'])->format('d/m/Y'),
            'estado' => $estado,
            'observacion' => $data['observacion'],
            'total_cajas' => $data['cajas']->count(),
            'cajas' => collect($data['cajas'])->transform(function($caja) use ($data) {
                return [
                    'id' => $caja->id,
                    'gestion' => $caja->gestion,
                    'cod_interno' => $caja->cod_interno,
                    'cod_almacen' => substr($caja->cod_almacen, 0, 8),
                    'cubi' => collect($caja->detalle_ingreso)->first()->codigo,
                    'carpetas' => collect($caja->detalle_salida()->where('salida_id', $data['id'])->get())->transform(function($detalle){
                        return $detalle->carpetas;
                    })
                ];
            }),
        ];
    }

    public function listTransform($data)
    {
        switch ($data['estado']) {
            case 0:
                $estado = "PENDIENTE";
                break;
            case 1:
                $estado = "APROBADO";
                break;
            case 2:
                $estado = "ENTREGADO";
                break;
            case 3:
                $estado = "RECHAZADO";
                break;
            default:
                $estado = "RECHAZADO";
        };

        $numero = explode("-", $data['codigo'], 3);

        return [
            'codigo' => $data['codigo'],
            'agencia' => $data['agencia']['razon_social'],
            'fecha_solicitud' => Carbon::parse($data['fecha_solicitud'])->format('d/m/Y'),
            'fecha_aprobacion' => is_null($data['fecha_aprobacion']) ? 'N/A' : Carbon::parse($data['fecha_aprobacion'])->format('d/m/Y'),
            'fecha_entrega' => is_null($data['fecha_entrega']) ? 'N/A' : Carbon::parse($data['fecha_entrega'])->format('d/m/Y'),
            'estado' => $estado,
            'observacion' => $data['observacion'],
            'total_cajas' => $data['cajas']->count(),
        ];
    }
}