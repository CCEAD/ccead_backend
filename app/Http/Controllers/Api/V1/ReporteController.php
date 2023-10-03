<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ApiController;
use Illuminate\Http\Request;
use App\Http\Resources\Reporte\TotalInvGesAgenciaCollection;
use App\Services\ReporteService;
use Illuminate\Support\Facades\DB;

class ReporteController extends ApiController
{
    private $service;

    public function __construct(ReporteService $service)
    {
        $this->service = $service;
    }

    public function totalInventarioAgencia()
    {
        $cajas = DB::table('cajas')
        ->select(DB::raw('COUNT(*) AS total'))
        ->where('agencia_id', get_user_agencia())
        ->first();

        $carpetas = DB::table('carpetas')
        ->select(DB::raw('COUNT(*) AS total'))
        ->join('cajas', 'cajas.id', '=', 'carpetas.caja_id')
        ->where('cajas.agencia_id', get_user_agencia())
        ->first();

        $ingresos = DB::table('ingresos')
        ->select(DB::raw('COUNT(*) AS total'))
        ->where('agencia_id', get_user_agencia())
        ->first();

        $salidas = DB::table('salidas')
        ->select(DB::raw('COUNT(*) AS total'))
        ->where('agencia_id', get_user_agencia())
        ->first();

        return $this->respond(['cajas' => $cajas, 'carpetas' => $carpetas, 'ingresos' => $ingresos, 'salidas' => $salidas]);
    }

    public function totalInvGestionAgencia(Request $request)
    {
        if ($request->filled('agencia')) {
            $agencia = $request->agencia;
        } else {
            $agencia = get_user_agencia();
        }
        
        $agencia = DB::table('agencias')->where('id', $agencia)->first();
        
        $cajas = DB::table('cajas')
            ->where('agencia_id', $agencia->id)
            ->when($request->gestion, function($query) use ($request) {
                $query->where('gestion', $request->gestion);
            })
            ->get();

        $group_cajas = $cajas->groupBy('gestion');

        // $total = collect($cajas)->count();

        return $this->respond($group_cajas);
    }

    public function totalInvAduanaAgencia(Request $request)
    {
        // if ($request->filled('agencia_id')) {
        //     $agencia_id = $request->agencia_id;
        // } else {
        //     $agencia_id = get_user_agencia();
        // }
        
        // $agencia = DB::table('agencias')->where('id', $agencia_id)->first();
        
        $carpetas = DB::table('carpetas')
            ->where('aduana_id', $agencia_id)
            ->get();

        // $group_cajas = $cajas->groupBy('gestion');

        // $total = collect($cajas)->count();

        return $this->service->totalInvAduanaAgencia($carpetas);
    }

    public function totalGeneralAgencia()
    {
        $agencias = DB::table('agencias')->where(function($query) {
            $query->where('id', '!=', 1)
                  ->where('estado', 1);
        })->get();

        $data = new TotalInvGesAgenciaCollection(collect(\App\Models\Agencia::hydrate($agencias->toArray())));

        return $this->service->totalGeneralAgencia($data);
    }

    public function ApiGeneralAgencia()
    {
        // $agencias = DB::table('agencias')->where(function($query) {
        //     $query->where('id', '=', 2)
        //           ->where('estado', 1)
        // })->get();

        $data = DB::table('agencias')
            ->join('cajas', 'agencias.id', '=', 'cajas.agencia_id')
            ->join('carpetas', 'cajas.id', '=', 'carpetas.caja_id')
            ->join('aduanas', 'carpetas.aduana_id', '=', 'aduanas.id')
            ->select('agencias.id', 'agencias.razon_social', 'cajas.gestion', 'cajas.cod_interno', 'carpetas.nro_declaracion', 'aduanas.codigo', 'aduanas.descripcion', 'carpetas.fecha_aceptacion', 'carpetas.digitalizado')
            ->where('agencias.id', 2)
            ->groupBy('carpetas.id')
            ->get();

        return new TotalInvGesAgenciaCollection(collect(\App\Models\Agencia::hydrate($data->toArray())));;

        // return new TotalInvGesAgenciaCollection(collect(\App\Models\Agencia::hydrate($agencias->toArray())));
    }

    public function totalCajasGrafico()
    {
        $cajas = DB::table('cajas')->where('agencia_id', get_user_agencia())->get();

        $data = collect($cajas)->groupBy('gestion')->map->count();

        return $data;
    }

    public function pdfDownload(Request $request)
    {
        return $this->service->manyPdfDownload($request);
    }
}
