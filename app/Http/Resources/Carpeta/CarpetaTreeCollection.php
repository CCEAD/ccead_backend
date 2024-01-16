<?php

namespace App\Http\Resources\Carpeta;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Caja\CajaTreeCollection;
use Carbon\Carbon;
use App\Models\Caja;

class CarpetaTreeCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $carpetasPorCaja = $this->collection->groupBy('caja_id');
        $carpetasPorGestion = [];

        foreach ($carpetasPorCaja as $cajaId => $carpetas) {
            $caja = Caja::find($cajaId);
            $gestion = $caja->gestion;

            $carpetasPorGestion[$gestion][] = [
                'id' => $caja->id,
                'text' => $caja->cod_interno,
                'type' => 'parent',
                'imageSrcEn' => 'img/box.png',
                'imageSrcDi' => 'img/box_disabled.png',
                'expanded' => true,
                'children' => $carpetas->map(function ($carpeta) {
                    return [
                        'id' => $carpeta->id,
                        'text' => $carpeta->nro_declaracion ? $carpeta->nro_declaracion : $carpeta->nro_registro,
                        'type' => 'child',
                        'digitalizado' => $carpeta->digitalizado == 1 ? true : false
                    ];
                }),
            ];
        }

        return collect($carpetasPorGestion)->map(function ($cajas, $gestion) {
            return [
                'text' => 'Gestión'.'-'.$gestion,
                'type' => 'grandparent',
                'imageSrc' => 'img/calendar-red.png',
                'expanded' => true,
                'children' => $cajas,
            ];
        })->values()->toArray();

        // $carpetas = $this->collection->transform(function($carpeta) {
        //     return [
        //         'id' => $carpeta->id,
        //         'digitalizado' => $carpeta->digitalizado,
        //         'nro_declaracion' => $carpeta->nro_declaracion,
        //         'nro_registro' => $carpeta->nro_registro,
        //         'created_at' => $carpeta->created_at,
        //         'caja_id' => 'id-'.$carpeta->caja_id,
        //         'caja' => $carpeta->caja,
        //     ];
        // })->groupBy('caja_id');

        // $cajas = [];
        // $idx = 0;
        // foreach ($carpetas as $key => $value) {
        //     $car = [];
        //     foreach ($value as $key_value => $item) {
        //         $car[$key_value] = [
        //             'id' => $item['id'],
        //             'digitalizado' => $item['digitalizado'] == 0 ? false : true,
        //             'nro_declaracion' => $item['nro_declaracion'],
        //             'nro_registro' => $item['nro_registro'],
        //             'interno' => $item['nro_declaracion'].' / '.$item['nro_registro'],
        //             'registrado' => Carbon::parse($item['created_at'])->format('d/m/Y')
        //         ];
        //     }

        //     switch ($value[0]['caja']['estado']) {
        //         case 0:
        //             $estado = "PENDIENTE";
        //             break;
        //         case 1:
        //             $estado = "EN ALMACÉN";
        //             break;
        //         case 2:
        //             $estado = "RETIRADA";
        //             break;
        //         default:
        //             $estado = "RETIRADA";
        //     };

        //     if ($value[0]['caja']['estado'] == 1) {
        //         $caja = Caja::find($value[0]['caja']['id']);
        //         $cubi = collect($caja->ingresos)->transform(function($ingreso) {
        //             return [
        //                 'active' => $ingreso->pivot->active,
        //                 'codigo' => $ingreso->pivot->ubigeo->codigo,
        //             ];
        //         })->first(function ($value, $key) {
        //             return $value['active'] == 1;
        //         });
        //     } else {
        //         $cubi = 'N/A';
        //     }

        //     $cajas[$idx] = [
        //         "id" => $value[0]['caja']['id'],
        //         "gestion" => $value[0]['caja']['gestion'],
        //         "cod_interno" => $value[0]['caja']['cod_interno'],
        //         "cod_almacen" => substr($value[0]['caja']['cod_almacen'], 0, 8),
        //         "observaciones" => $value[0]['caja']['observaciones'],
        //         "estado" => $estado,
        //         'ubigeo' => $cubi,
        //         "carpetas" => $car,
        //         "contenido" => count($car) > 0 ? true : false,
        //         "created_at" => Carbon::parse($value[0]['caja']['created_at'])->format('d/m/Y'),
        //         "updated_at" => Carbon::parse($value[0]['caja']['updated_at'])->format('d/m/Y'),
        //         "year" => $value[0]['caja']['year'],
                
        //     ];
        //     $idx++;
        // }
        // return collect($cajas)->groupBy('year');
    }
}
