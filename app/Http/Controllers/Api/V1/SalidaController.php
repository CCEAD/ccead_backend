<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Salida;
use App\Models\Carpeta;
use App\Http\Controllers\Api\V1\ApiController;
use Illuminate\Http\Request;
use App\Filters\SalidaSearch\SalidaSearch;
use App\Http\Requests\Salida\StoreSalidaRequest;
use App\Http\Resources\Salida\SalidaResource;
use App\Http\Resources\Salida\SalidaEditResource;
use App\Http\Resources\Salida\SalidaCollection;
use App\Http\Resources\Salida\SalidaAgenciaCollection;
use App\Http\Resources\Salida\SalidaAdminCollection;
use App\Http\Resources\Salida\SalidaDetalleResource;
use App\Services\SalidaService;
use Illuminate\Support\Facades\DB;

class SalidaController extends ApiController
{
    private $salida;
    private $service;

    public function __construct(Salida $salida, SalidaService $service)
    {
        $this->salida = $salida;
        $this->service = $service;
    }

    public function store(StoreSalidaRequest $request)
    {
        DB::beginTransaction();

        try {
            $salida = $this->salida->create([
                'fecha_solicitud' => $request->salida['fecha_solicitud'],
                'observacion' => $request->salida['observacion'],
                'agencia_id' => get_user_agencia(),
            ]);

            foreach ($request->cajas as $caja) {
                $id = DB::table('caja_salida')->insertGetId(
                    ['salida_id' => $salida->id, 'caja_id' => $caja]
                );

                $carpetas = DB::table('carpetas')->where(function($q) use ($caja) {
                    $q->where('caja_id', $caja)
                        ->where('estado', true);
                })->get();

                foreach ($carpetas as $carpeta) {
                    DB::table('carpeta_caja_salida')->insert([
                        'caja_salida_id' => $id,
                        'carpeta_id' => $carpeta->id
                    ]);
                }

                // if (count($item) > 0) {
                //     foreach ($item as $key_item => $value) {
                //         DB::table('carpeta_caja_salida')->insert([
                //             'caja_salida_id' => $id,
                //             'carpeta_id' => $value
                //         ]);
                //     }
                // }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }

        return $this->respondCreated($salida);
    }

    public function edit(Salida $salida)
    {
        return new SalidaEditResource($salida);
    }

    public function update(Request $request, Salida $salida)
    {
        DB::beginTransaction();
        try {
            if (verificar_estado_salida($salida)) {
                return $this->respond(['success' => false, 'message' => message('MSG016')], 406);
            }

            $salida->update([
                'fecha_solicitud' => $request->salida['fecha_solicitud'],
                'observacion' => $request->salida['observacion'],
            ]);

            DB::table('caja_salida')->where('salida_id', $salida->id)->delete();

            foreach ($request->cajas as $caja) {
                $id = DB::table('caja_salida')->insertGetId(
                    ['salida_id' => $salida->id, 'caja_id' => $caja]
                );

                $carpetas = DB::table('carpetas')->where('caja_id', $caja)->get();

                foreach ($carpetas as $carpeta) {
                    DB::table('carpeta_caja_salida')->insert([
                        'caja_salida_id' => $id,
                        'carpeta_id' => $carpeta->id
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }
        return $this->respondUpdated($salida);
    }

    public function destroy(Salida $salida)
    {
        DB::beginTransaction();

        try {
            if (verificar_estado_salida($salida)) {
                return $this->respond(['success' => false, 'message' => message('MSG017')], 406);
            }

            $salida->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }
        return $this->respondCanceled();
    }

    public function salidaPdf(Salida $salida)
    {
        return $this->service->singlePdfDownload($salida);
    }

    public function aprobarSalida(Request $request)
    {
        DB::beginTransaction();

        try {
            $salida = $this->salida->findOrFail($request->salida_id);

            if ($salida->estado != 0) {
                return $this->respond(['success' => false, 'message' => message('MSG019')], 406);
            }

            $salida->update(['estado' => 1, 'fecha_aprobacion' => date('Y-m-d'), 'fecha_entrega' => $request->fecha_entrega]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }
        return $this->respond(['success' => true, 'message' => message('MSG003')]);
    }

    public function rechazarSalida(Request $request)
    {
        DB::beginTransaction();

        try {
            $salida = $this->salida->findOrFail($request->salida_id);

            if ($salida->estado == 1 || $salida->estado == 2 || $salida->estado == 3) {
                return $this->respond(['success' => false, 'message' => message('MSG022')], 406);
            }
            
            $salida->update(['estado' => 3]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }
        return $this->respond(['success' => true, 'message' => message('MSG003')]);
    }

    public function ejecutarSalida(Request $request)
    {
        DB::beginTransaction();

        try {
            $salida = $this->salida->findOrFail($request->salida_id);

            if ($salida->estado == 0 || $salida->estado == 2 || $salida->estado == 3) {
                return $this->respond(['success' => false, 'message' => message('MSG023')], 406);
            }
            
            $salida->estado = 2;
            $salida->save();

            foreach ($salida->cajas as $key => $item) {

                DB::table('cajas')->where('id', $item->id)->update(['estado' => 2]);

                // DB::table('caja_ingreso')->where(function($query) use ($item) {
                //     $query->where('caja_id', $item->id)
                //       ->where('active', 1);
                // })->update(['active' => 0]);

                $carpetas = $item->detalle_salida()->where('caja_salida_id', $item->pivot->id)->get()->map(function ($row, $key) {
                    return $row->carpeta_id;
                });   
                
                Carpeta::whereIn('id', $carpetas)->update(['estado' => 2]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }
        return $this->respond(['success' => true, 'message' => message('MSG003')]);
    }

    public function salidasPorAgenciaAdmin(Request $request)
    {
        // $salidas = $this->salida->salidasPorAgencia($request->id)->orderBy('id', $request->sort)->paginate($request->per_page);
        // return new SalidaAdminCollection($salidas);

        if ($request->filled('filter.filters')) {
            return new SalidaAdminCollection(SalidaSearch::apply($request, $this->salida));
        }

        $salidas = SalidaSearch::checkSortFilter($request, $this->salida->newQuery());

        return new SalidaAdminCollection($salidas->salidasPorAgencia($request->id)->paginate($request->take)); 
    }

    public function salidasPorAgencia(Request $request)
    {
        // $salidas = $this->salida->salidasPorAgencia(get_user_agencia())->orderBy('id', $request->sort)->paginate($request->per_page);
        // return new SalidaCollection($salidas);

        if ($request->filled('filter.filters')) {
            return new SalidaAgenciaCollection(SalidaSearch::apply($request, $this->salida));
        }

        $salidas = SalidaSearch::checkSortFilter($request, $this->salida->newQuery());

        return new SalidaAgenciaCollection($salidas->salidasPorAgencia(get_user_agencia())->paginate($request->take)); 
    }

    public function verDetalleSalida(Request $request)
    {
        $salida = $this->salida->findOrFail($request->id);
        return new SalidaDetalleResource($salida);
    }

    public function listPdf(Request $request)
    {
        return $this->service->manyPdfDownload($request);
    }

    public function listExcel(Request $request) 
    {
        return $this->service->manyExcelDownload($request);
    }
}
