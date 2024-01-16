<?php

namespace App\Http\Resources\Caja;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CajaTreeCollection extends ResourceCollection
{
    public function toArray($request)
    {
        // return $this->collection->transform(function($caja){
        //     return new CajaResource($caja);
        // })->groupBy('year');

        $cajasPorGestion = $this->collection->groupBy('gestion');

        return $cajasPorGestion->map(function ($cajas, $gestion) {
            return [
                'text' => 'Gestión'.'-'.$gestion,
                'type' => 'grandparent',
                'imageSrc' => 'img/calendar-red.png',
                'expanded' => false,
                'children' => $cajas->map(function ($caja) {
                    return [
                        'id' => $caja->id,
                        'text' => $caja->cod_interno,
                        'type' => 'parent',
                        'imageSrcEn' => 'img/box.png',
                        'imageSrcDi' => 'img/box_disabled.png',
                        'expanded' => false,
                        'children' => $caja->carpetas->map(function ($carpeta) {
                            return [
                                'id' => $carpeta->id,
                                'text' => $carpeta->nro_declaracion ? $carpeta->nro_declaracion : $carpeta->nro_registro,
                                'type' => 'child',
                                'digitalizado' => $carpeta->digitalizado == 1 ? true : false
                            ];
                        }),
                    ];
                }),
            ];
        })->values()->toArray();
    }
}