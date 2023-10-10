<?php

namespace App\Transformers;
use Carbon\Carbon;

class CajaTransformer extends Transformer
{
    protected $resourceName = 'caja';

    public function transform($data)
    {
        return [
            'id' => $data['id'],
            'detalle' => $data['agencia']['razon_social'].' | Gestión: '.$data['gestion'].' | Interno: '.$data['cod_interno'].' | Cant.Carpetas: '.$data['cant_carpetas'],
            'cubi' => $data['detalle_ingreso'][0]['codigo']
        ];
    }

    public function listTransform($data)
    {
        switch ($data['estado']) {
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

        return [
            'id' => $data['id'],
            'gestion' => $data['gestion'],
            'cod_interno' => $data['cod_interno'],
            'cant_carpetas' => $data['cant_carpetas'],
            'cant_actual' => $data->carpetas()->where('estado', true)->count(),
            'reg_inicial' => $data['reg_inicial'],
            'reg_final' => $data['reg_final'],
            'estado' => $estado,
            'agencia' => $data['agencia']['razon_social'],
            'created' => Carbon::parse($data['created_at'])->format('d/m/Y'),
        ];
    }
}

