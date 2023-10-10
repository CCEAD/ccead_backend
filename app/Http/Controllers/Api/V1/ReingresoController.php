<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Reingreso;
use App\Models\Carpeta;
use App\Models\Salida;
use App\Http\Controllers\Api\V1\ApiController;
use Illuminate\Http\Request;
use App\Services\ReingresoService;
use App\Http\Resources\Reingreso\ReingresoResource;
use App\Http\Resources\Salida\SalidaReingresoResource;
use Illuminate\Support\Facades\DB;

class ReingresoController extends ApiController
{
    private $reingreso;
    private $service;

    public function __construct(Reingreso $reingreso, ReingresoService $service)
    {
        $this->reingreso = $reingreso;
        $this->service = $service;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $salida = Salida::where('id', $request->reingreso['salida_id'])->first();

            if ($salida->estado != 2) {
                return $this->respond(['success' => false, 'message' => message('MSG024')], 406);
            }

            if ($salida->ingresado) {
                return $this->respond(['success' => false, 'message' => message('MSG021')], 406);
            }
    
            $reingreso = $this->reingreso->create([
                'fecha' => date('Y-m-d H:i:s'),
                'observacion' => $request->reingreso['observacion'],
                'agencia_id' => $salida->agencia_id,
                'salida_id' => $salida->id,
            ]);

            $salida->ingresado = 1;
            $salida->save();

            foreach ($request->cajas as $key => $caja) {
                if ($caja['ingresa']) {
                    $id = DB::table('caja_reingreso')->insertGetId(
                        ['reingreso_id' => $reingreso->id, 'caja_id' => $key]
                    );
    
                    DB::table('cajas')->where('id', $key)->update(['estado' => 1]);
    
                    // DB::table('caja_ingreso')->where(function($query) use ($key) {
                    //     $query->where('caja_id', $key)
                    //     ->where('active', 0);
                    // })->update(['active' => 1]);
    
                    $carpetas = DB::table('carpetas')->where(function($query) use ($key) {
                        $query->where('estado', 2)
                        ->where('caja_id', $key);
                    })->get();
            
                    $fil_carpetas = $carpetas->whereNotIn('id', $caja['retiradas'])->flatten()->map(function ($item, $key) {
                        return $item->id;
                    });
    
                    Carpeta::whereIn('id', $fil_carpetas)->update(['estado' => 1]);
    
                    Carpeta::whereIn('id', $caja['retiradas'])->update(['estado' => 3]);
    
                    foreach ($caja as $keyitem => $item) {
                        $data[$keyitem] = $item;
    
                        if ($keyitem == 'retiradas' && count($item) > 0) {
                            foreach ($item as $carpeta) {
                                DB::table('carpeta_caja_reingreso')->insert([
                                    'caja_reingreso_id' => $id,
                                    'carpeta_id' => $carpeta,
                                    'type' => 0
                                ]);
                            }
                        }
    
                        if ($keyitem == 'agregadas' && count($item) > 0) {
                            foreach ($item as $carpeta) {
                                DB::table('carpeta_caja_reingreso')->insert([
                                    'caja_reingreso_id' => $id,
                                    'carpeta_id' => $carpeta,
                                    'type' => 1
                                ]);
    
                                DB::table('carpetas')->where('id', $carpeta)->update(['estado' => 1]);
                            }
                        }
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }
        return $this->respond(['success' => true, 'message' => message('MSG003')]);
    }

    public function obtenerDatosReingreso(Request $request)
    {
        $salida = Salida::where('id', $request->salida_id)->first();

        return new SalidaReingresoResource($salida);
    }

    public function verDetalleReingreso(Request $request)
    {

        $reingreso = $this->reingreso->where('salida_id', $request->salida_id)->first();

        return new ReingresoResource($reingreso);
    }

    public function reingresoPdf(Reingreso $reingreso)
    {
        return $this->service->singlePdfDownload($reingreso);
    }
}
