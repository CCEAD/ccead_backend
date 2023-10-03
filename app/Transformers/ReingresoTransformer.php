<?php

namespace App\Transformers;
use Carbon\Carbon;

class ReingresoTransformer extends Transformer
{
    protected $resourceName = 'reingreso';

    public function transform($data)
    {
        return [
            'id' => $data['id'],
            'codigo' => $data['codigo'],
            'fecha' => $data['fecha'],
            'observacion' => $data['observacion'],
            'estado' => $data['estado'] === 0 ? 'ANULADO' : 'RECIBIDO',
            'agencia' => $data['agencia']['razon_social'],
            'salida' => $data['salida']['codigo'],
            'total_cajas' => $data['cajas']->count(),
            'cajas' => collect($data['cajas'])->transform(function($caja) use ($data) {
                
                $carpetas = collect($caja->detalle_reingreso()->where('reingreso_id', $data['id'])->get())->transform(function($detalle){
                    return [
                        'tipo' => $detalle->type == 0 ? 'retiradas' : 'agregadas',
                        'carpetas' => $detalle->carpetas
                    ];
                })->groupBy('tipo');

                $data = [];

                foreach ($carpetas as $ki => $carpeta) {
                    $c = [];
                    foreach ($carpeta as $kj => $item) {
                        $c[$kj] = $item['carpetas'];
                    }

                    $data[$ki] = $c;
                }

                return [
                    'id' => $caja->id,
                    'gestion' => $caja->gestion,
                    'cod_interno' => $caja->cod_interno,
                    'cubi' => collect($caja->detalle_ingreso)->first()->codigo,
                    'cod_almacen' => substr($caja->cod_almacen, 0, 8),
                    'carpetas' => $data
                ];
            }),
        ];
    }
}