<?php

namespace App\Http\Resources\Reporte;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TotalInvGesAgenciaCollection extends ResourceCollection
{
    public static $wrap = null;

    public function toArray($request)
    {
        return $this->collection->transform(function($item) {

            // $query = DB::table('carpetas')
            // ->join('cajas', 'cajas.id', '=', 'carpetas.caja_id')
            // ->where('cajas.agencia_id', $item->id)
            // ->select('carpetas.*')
            // ->get();
            
            // $carpetas = collect(\App\Models\Carpeta::hydrate($query->toArray()));

            // return [
            //     'id' => $item->id,
            //     'razon_social' => $item->razon_social,
            //     'contenido' => collect($carpetas)->transform(function($carpeta) {
            //         return [
            //             'nro_carpeta' => $carpeta->nro_declaracion,
            //             'caja_gestion' => $carpeta->caja->gestion,
            //             'aduana' => $carpeta->aduana ? $carpeta->aduana->codigo.'-'.$carpeta->aduana->descripcion : 'N/A',
            //             'digitalizado' => $carpeta->digitalizado == 0 ? 'NO' : 'SI'
            //         ];
            //     })
            // ];

            // $carpetas = Carpeta::select('carpetas.*')
            //     ->join('cajas', 'carpetas.caja_id', '=', 'cajas.id')
            //     ->where('cajas.agencia_id', $item->id)
            //     ->get();

            // return [
            //     'id' => $item->id,
            //     'razon_social' => $item->razon_social,
            //     //'carpetas' => $carpetas,
            //     'contenido' => collect($carpetas)->transform(function($carpeta) {
            //         return [
            //             'nro_carpeta' => $carpeta->nro_declaracion,
            //             'caja_gestion' => $carpeta->caja->gestion,
            //             'aduana' => $carpeta->aduana ? $carpeta->aduana->codigo.'-'.$carpeta->aduana->descripcion : 'N/A',
            //             'digitalizado' => $carpeta->digitalizado == 0 ? 'NO' : 'SI'
            //        ];
            //     })
            // ];

            return [
                'id' => $item->id,
                'razon_social' => $item->razon_social,
                'gestion' => $item->gestion,
                'cod_interno' => $item->cod_interno,
                'nro_declaracion' => $item->nro_declaracion,
                'aduana' => $item->codigo.'-'.$item->descripcion,
                'fecha' => Carbon::parse($item->fecha_aceptacion)->format('d/m/Y'),
                'digitalizado' => $item->digitalizado == 0 ? 'NO' : 'SI'
            ];
        });
    }
}
