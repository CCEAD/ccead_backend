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

    public function totalCajasGrafico()
    {
        $cajas = DB::table('cajas')->where('agencia_id', get_user_agencia())->get();

        $data = $cajas->groupBy('gestion')->map(function ($item, $key) {
            return [
                'name' => $key,
                'y' => $item->count(),
            ];
        })->values()->toArray();

        return $data;
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
        $carpetas = DB::table('carpetas')
        ->join('cajas', 'carpetas.caja_id', '=', 'cajas.id')
        ->join('aduanas', 'carpetas.aduana_id', '=', 'aduanas.id')
        ->select('cajas.id', 'cajas.gestion', 'carpetas.id', 'carpetas.nro_declaracion', 'carpetas.nro_registro', DB::raw("CONCAT(aduanas.codigo, '-', aduanas.descripcion) as aduana"))
        ->where(function($query) use ($request) {
            $query->where('carpetas.aduana_id', $request->aduana)
                  ->where('cajas.agencia_id', get_user_agencia());
        })->when($request->gestion, function($query) use ($request) {
            $query->where('cajas.gestion', $request->gestion);
        })->get()->groupBy('gestion');

        $data = $carpetas->map(function ($items, $gestion) {
            return $items->toArray();
        })->toArray();

        return $this->respond($data);
    }

    public function totalCanalGrafico(Request $request)
    {
        $result = DB::table('carpetas')
            ->join('cajas', 'carpetas.caja_id', '=', 'cajas.id')
            ->join('aduanas', 'carpetas.aduana_id', '=', 'aduanas.id')
            ->select('carpetas.canal', 'aduanas.descripcion')
            ->selectRaw('COUNT(*) as count')
            ->where('cajas.agencia_id', get_user_agencia())
            ->when($request->gestion, function($query) use ($request) {
                $query->where('cajas.gestion', $request->gestion);
            })
            ->when($request->aduana, function($query) use ($request) {
                $query->where('aduanas.id', $request->aduana);
            })
            ->orderBy('aduanas.id')
            ->groupBy('carpetas.canal', 'aduanas.descripcion', 'aduanas.id')
            ->get();

        $finalResult = [];

        // Array para almacenar la suma total por caja
        $totalCajas = [];
        $totalCanales = [];

        foreach ($result as $row) {
            $canalName = '';
            switch ($row->canal) {
                case 0:
                    $canalName = 'Canal Rojo';
                    $color = '#FF9494';
                    break;
                case 1:
                    $canalName = 'Canal Verde';
                    $color = '#94FF9E';
                    break;
                case 2:
                    $canalName = 'Canal Amarillo';
                    $color = '#FDFF94';
                    break;
            }

            if (!isset($finalResult[$canalName])) {
                $finalResult[$canalName] = ['type' => 'column', 'name' => $canalName, 'color' => $color, 'data' => []];
            }

            $cajaName = $row->descripcion;
            $finalResult[$canalName]['data'][$cajaName] = $row->count;

            // Suma total por caja
            if (!isset($totalCajas[$cajaName])) {
                $totalCajas[$cajaName] = 0;
            }
            $totalCajas[$cajaName] += $row->count;

            //Suma total por canal
            if (!isset($totalCanales[$canalName])) {
                $totalCanales[$canalName] = 0;
            }
            $totalCanales[$canalName] += $row->count;
        }

        // Agregar el objeto 'Total Cajas' al resultado final
        $totalCajasItem = [
            'type' => 'spline',
            'name' => 'Total Trámites',
            'color' => '#287729',
            'data' => $totalCajas,
            'marker' => ['lineWidth' => 2, 'lineColor' => '#8e978d', 'fillColor' => 'white']
        ];
        $finalResult[] = $totalCajasItem;

        // Agregar el objeto 'Total Canales' al resultado final
        $totalCanalesItem = [
            'type' => 'pie',
            'name' => 'Total',
            'data' => array_map(function ($canal, $count) {
                switch ($canal) {
                    case 'Canal Rojo':
                        $color = '#FF9494';
                        break;
                    case 'Canal Verde':
                        $color = '#94FF9E';
                        break;
                    case 'Canal Amarillo':
                        $color = '#FDFF94';
                        break;
                }

                $data = ['name' => $canal, 'y' => $count, 'color' => $color];

                if ($canal === 'Canal Rojo') {
                    $data['dataLabels'] = [
                        'enabled' => true,
                        'distance' => -50,
                        'format' => '{point.total} C.',
                        'style' => ['fontSize' => '15px']
                    ];
                }
                return $data;

            }, array_keys($totalCanales), $totalCanales),
            'center' => [75, 65],
            'size' => 100,
            'innerSize' => '70%',
            'showInLegend' => false,
            'dataLabels' => ['enabled' => false]
        ];

        $finalResult[] = $totalCanalesItem;

        // Convierte el arreglo asociativo en un arreglo indexado
        $finalResult = array_values($finalResult);

        return $this->respond($finalResult);
    }

    public function totalGestionGrafico(Request $request)
    {
        $resultado = DB::table('cajas')
            ->join('carpetas', 'cajas.id', '=', 'carpetas.caja_id')
            ->join('aduanas', 'aduanas.id', '=', 'carpetas.aduana_id')
            ->select('cajas.gestion', 'aduanas.descripcion')
            ->groupBy('cajas.gestion', 'aduanas.descripcion')
            ->orderBy('aduanas.id', 'asc')
            ->selectRaw('COUNT(*) as count')
            ->where('cajas.agencia_id', get_user_agencia())
            ->when($request->gestion, function($query) use ($request) {
                $query->where('cajas.gestion', $request->gestion);
            })
            ->when($request->aduana, function($query) use ($request) {
                $query->where('aduanas.id', $request->aduana);
            })
            ->get();

        $agrupado = $resultado->groupBy('gestion');

        $finalResult = [];

        foreach ($agrupado as $gestion => $data) {
            $formattedData = $data->pluck('count', 'descripcion')->toArray();
            $finalResult[] = [
                'name' => $gestion,
                'data' => $formattedData,
            ];
        }

        return $this->respond($finalResult);
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

    public function pdfDownload(Request $request)
    {
        return $this->service->manyPdfDownload($request);
    }
}
