<?php

namespace App\Transformers;
use Carbon\Carbon;

class IngresoTransformer extends Transformer
{
    protected $resourceName = 'ingreso';

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

                if ($data['estado'] == 2) {
                    $cubi = collect($caja->detalle_ingreso)->first()->codigo;
                } else {
                    $cubi = null;
                }

                return [
                    'id' => $caja->id,
                    'gestion' => $caja->gestion,
                    'cod_interno' => $caja->cod_interno,
                    'cubi' => $cubi,
                    'cod_almacen' => substr($caja->cod_almacen, 0, 8),
                    'carpetas' => $caja->carpetas
                ];
            }),
        ];
    }

    public function ingresoCubi($data)
    {
        return [
            'cajas' => collect($data['cajas'])->transform(function($caja){
                return [
                    'id' => $caja['id'],
                    'detalle' => $caja['agencia']['razon_social'].' | Gestión: '.$caja['gestion'].' | Interno: '.$caja['cod_interno'].' | Cant.Carpetas: '.$caja['cant_carpetas'],
                    'cubi' => $caja['pivot']['ubigeo']['codigo']
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