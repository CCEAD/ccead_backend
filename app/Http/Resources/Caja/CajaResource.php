<?php

namespace App\Http\Resources\Caja;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CajaResource extends JsonResource
{
    public function toArray($request)
    {
        switch ($this->estado_id) {
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
        
        if ($this->estado == 1) {
            $cubi = collect($this->ingresos)->transform(function($ingreso) {
                return [
                    'active' => $ingreso->pivot->active,
                    'codigo' => $ingreso->pivot->ubigeo->codigo,
                ];
            })->first(function ($value, $key) {
                return $value['active'] == 1;
            });
        } else {
            $cubi = 'N/A';
        }

        return [
            'id' => $this->id,
            'gestion' => $this->gestion,
            'cod_interno' => $this->cod_interno,
            'cod_almacen' => substr($this->cod_almacen, 0, 8),
            'cant_carpetas' => $this->cant_carpetas,
            'stock_carpetas' => $this->carpetas()->where('estado', true)->count(),
            'observaciones' => $this->observaciones,
            'estado' => ['title' => $estado],
            'ubigeo' => $cubi,
            'reg_inicial' => $this->reg_inicial,
            'reg_final' => $this->reg_final,
            'agencia_id' => $this->agencia_id,
            // 'carpetas' => collect($this->carpetas)->transform(function($carpeta){
            //     return [
            //         'id' => $carpeta->id,
            //         'digitalizado' => $carpeta->digitalizado == 0 ? false : true,
            //         'nro_declaracion' => $carpeta->nro_declaracion,
            //         'nro_registro' => $carpeta->nro_registro,
            //         'interno' => $carpeta->nro_declaracion.' / '.$carpeta->nro_registro,
            //         'registrado' => Carbon::parse($this->created_at)->format('d/m/Y')
            //     ];
            // }),
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d/m/Y'),
        ];
    }
}
