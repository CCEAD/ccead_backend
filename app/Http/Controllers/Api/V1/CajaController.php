<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Caja;
use App\Models\Carpeta;
use App\Http\Controllers\Api\V1\ApiController;
use Illuminate\Http\Request;
use App\Http\Requests\Caja\StoreCajaRequest;
use App\Http\Requests\Caja\UpdateCajaRequest;
use App\Filters\CajaSearch\CajaSearch;
use App\Filters\CajaSearch\Searches\CajasFilter;
use App\Http\Resources\Caja\CajaResource;
use App\Http\Resources\Caja\CajaEditResource;
use App\Http\Resources\Caja\CajaCarpetasDetailResource;
use App\Http\Resources\Caja\CajaCollection;
use App\Http\Resources\Caja\CajaEditCollection;
use App\Http\Resources\Caja\CajaAdminCollection;
use App\Http\Resources\Caja\CajaTreeCollection;
use App\Http\Resources\Carpeta\CarpetaTreeCollection;
use App\Filters\CarpetaSearch\Searches\CarpetasFilter;
use App\Services\CajaService;
use Illuminate\Support\Facades\DB;

class CajaController extends ApiController
{
    private $caja;
    private $service;

    public function __construct(Caja $caja, CajaService $service)
    {
        $this->caja = $caja;
        $this->service = $service;
    }

    public function index(Request $request)
    {
        if ($request->filled('filter.filters')) {
            if ($request->input('filter.filters')[0]['field'] == 'code') {
                $codigoCarpeta = $request->input('filter.filters')[0]['value'];

                $caja = $this->caja->whereHas('carpetas', function ($query) use ($codigoCarpeta) {
                    $query->where('nro_declaracion', $codigoCarpeta)
                        ->orWhere('nro_registro', $codigoCarpeta);
                })->where('agencia_id', get_user_agencia())->paginate(1);

                if ($caja) {
                    return new CajaCollection($caja);
                } else {
                    return new CajaCollection([]);
                }
            } else {
                return new CajaCollection(CajaSearch::apply($request, $this->caja));
            }
        }

        $cajas = CajaSearch::checkSortFilter($request, $this->caja->newQuery());

        return new CajaCollection($cajas->cajasPorAgencia(get_user_agencia())->paginate($request->take)); 
    }

    public function carpetasPorCaja(Caja $caja)
    {
        return new CajaCarpetasDetailResource($caja);
    }

    public function storeAdmin(StoreCajaRequest $request)
    {
        DB::beginTransaction();

        try {
            $caja = $this->caja->create($request->all());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }

        return $this->respondCreated($caja);
    }

    public function storeAgencia(StoreCajaRequest $request)
    {
        DB::beginTransaction();

        try {
            $caja = $this->caja->create([
                'gestion' => $request->gestion, 
                'cod_interno' => $request->cod_interno, 
                'cod_almacen' => $request->cod_almacen, 
                'cant_carpetas' => $request->cant_carpetas,
                'reg_inicial' => $request->reg_inicial, 
                'reg_final' => $request->reg_final,
                'observaciones' => $request->observaciones, 
                'agencia_id' => get_user_agencia(), 
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }

        return $this->respondCreated($caja);
    }

    public function edit(Caja $caja)
    {
        return new CajaEditResource($caja);
    }

    public function update(UpdateCajaRequest $request, Caja $caja)
    {
        DB::beginTransaction();

        try {
            if ($caja->estado_id >= 1) {
                return response()->json(['message' => message('MSG025'), 'success' => false], 406);
            }
            $caja->update($request->all());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }

        return $this->respondUpdated($caja);
    }

    public function destroy(Request $request)
    {
        try {
            $data = [];
            $cajas = $this->caja->find($request->cajas);
            foreach ($cajas as $caja) {
                $model = $caja->secureDeleteState();
                if ($model) {
                    $data[] = $caja;
                }
            }
        } catch (Exception $e) {
            return $this->respondInternalError();
        }
        return $this->respondDeleted($data);
    }

    public function listCarpetasCajaPdf(Caja $caja)
    {
        return $this->service->listCarpetasCajaPdf($caja);
    }

    public function listCarpetasCajaExcel(Caja $caja)
    {
        return $this->service->listCarpetasCajaExcel($caja);
    }

    public function cajasPorAgenciaAdmin(CajasFilter $filters, Request $request)
    {
        // if($request->filled('nro')) {
        //     $cajas = Caja::filter($filters)->cajasPorAgencia($request->id)->orderBy('created_at', $request->sort)->paginate($request->per_page);
        //     return new CajaCollection($cajas);
        // } 

        // $cajas = $this->caja->cajasPorAgencia($request->id)->orderBy('created_at', $request->sort)->paginate($request->per_page);
        // return new CajaCollection($cajas);
        if ($request->filled('filter.filters')) {
            if ($request->input('filter.filters')[0]['field'] == 'code') {
                $codigoCarpeta = $request->input('filter.filters')[0]['value'];

                $caja = $this->caja->whereHas('carpetas', function ($query) use ($codigoCarpeta) {
                    $query->where('nro_declaracion', $codigoCarpeta)
                        ->orWhere('nro_registro', $codigoCarpeta);
                })->where('agencia_id', $request->id)->paginate(1);

                if ($caja) {
                    return new CajaCollection($caja);
                } else {
                    return new CajaCollection([]);
                }
            } else {
                return new CajaCollection(CajaSearch::apply($request, $this->caja));
            }
        }

        $cajas = CajaSearch::checkSortFilter($request, $this->caja->newQuery());

        return new CajaCollection($cajas->cajasPorAgencia($request->id)->paginate($request->take)); 
    }

    public function cajasPorAgencia(CarpetasFilter $filters, Request $request)
    {
        if($request->filled('nro')) {
            $carpetas = Carpeta::filter($filters)->get();
            return new CarpetaTreeCollection($carpetas);
        } 

        $cajas = $this->caja->cajasPorAgencia(get_user_agencia())->get();
        
        return new CajaTreeCollection($cajas);
         
    }

    public function cajasPendientesPorAgenciaAdmin(Request $request)
    {
        $cajas = $this->caja->cajasPorAgencia($request->id)->pendiente()->get();
        return new CajaEditCollection($cajas);
    }

    public function cajasPendientesPorAgencia(Request $request)
    {
        // $cajas = $this->caja->cajasPorAgencia(get_user_agencia())->pendiente()->get();
        // return new CajaEditCollection($cajas);

        if ($request->filled('filter.filters')) {
            return new CajaCollection(CajaSearch::apply($request, $this->caja));
        }

        $cajas = CajaSearch::checkSortFilter($request, $this->caja->newQuery());

        return new CajaCollection($cajas->cajasPorAgencia(get_user_agencia())->pendiente()->get()); 
    }

    public function cajasActivasPorAgenciaAdmin(Request $request)
    {
        $cajas = $this->caja->cajasPorAgencia($request->id)->activa()->get();
        return new CajaEditCollection($cajas);
    }

    public function cajasActivasPorAgencia(Request $request)
    {
        // $cajas = $this->caja->cajasPorAgencia(get_user_agencia())->activa()->get();
        // return new CajaEditCollection($cajas);

        if ($request->filled('filter.filters')) {
            return new CajaCollection(CajaSearch::apply($request, $this->caja));
        }

        $cajas = CajaSearch::checkSortFilter($request, $this->caja->newQuery());

        return new CajaCollection($cajas->cajasPorAgencia(get_user_agencia())->activa()->get());
    }

    public function generarCajaCubiPdf(Caja $caja)
    {
        return $this->service->generateCubiPdf($caja);
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
