<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Ingreso;
use App\Http\Controllers\Api\V1\ApiController;
use Illuminate\Http\Request;
use App\Filters\IngresoSearch\IngresoSearch;
use App\Http\Resources\Ingreso\IngresoResource;
use App\Http\Resources\Ingreso\IngresoEditResource;
use App\Http\Resources\Ingreso\IngresoCollection;
use App\Http\Resources\Ingreso\IngresoAgenciaCollection;
use App\Http\Resources\Ingreso\IngresoAdminCollection;
use App\Http\Resources\Ingreso\IngresoDetalleResource;
use App\Services\IngresoService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SolicitudIngreso;

class IngresoController extends ApiController
{
    private $ingreso;
    private $service;

    public function __construct(Ingreso $ingreso, IngresoService $service)
    {
        $this->ingreso = $ingreso;
        $this->service = $service;
    }
    
    public function storeAdmin(Request $request)
    {
        DB::beginTransaction();

        try {
            $ingreso = $this->ingreso->create([
                'fecha_solicitud' => $request->ingreso['fecha_solicitud'],
                'observacion' => $request->ingreso['observacion'],
                'agencia_id' => $request->ingreso['agencia_id'],
            ]);

            $ingreso->cajas()->attach($request->cajas);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }

        return $this->respondCreated();
    }

    public function storeAgencia(Request $request)
    {
        DB::beginTransaction();

        try {
            $ingreso = $this->ingreso->create([
                'fecha_solicitud' => $request->ingreso['fecha_solicitud'],
                'observacion' => $request->ingreso['observacion'],
                'agencia_id' => get_user_agencia(),
            ]);

            $ingreso->cajas()->attach($request->cajas);

            $usuarios = DB::table('users')->where('agencia_id', 1)->get();

            Mail::to(collect($usuarios)->pluck('email'))->send(new SolicitudIngreso($ingreso));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }

        return $this->respondCreated();
    }

    public function edit(Ingreso $ingreso)
    {
        return new IngresoEditResource($ingreso);
    }

    public function update(Request $request, Ingreso $ingreso)
    {
        DB::beginTransaction();
        try {
            if (verificar_estado_ingreso($ingreso)) {
                return $this->respond(['success' => false, 'message' => message('MSG016')], 406);
            }

            $ingreso->fecha_solicitud = $request->ingreso['fecha_solicitud'];
            $ingreso->observacion = $request->ingreso['observacion'];
            $ingreso->save();

            $ingreso->cajas()->sync($request->cajas);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }
        return $this->respondUpdated();
    }

    public function destroy(Ingreso $ingreso)
    {
        DB::beginTransaction();

        try {
            if (verificar_estado_ingreso($ingreso)) {
                return $this->respond(['success' => false, 'message' => message('MSG017')], 406);
            }

            $ingreso->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }
        return $this->respondCanceled();
    }

    public function ingresoPdf(Ingreso $ingreso)
    {
        return $this->service->singlePdfDownload($ingreso);
    }

    public function generarListaCubiPdf(Ingreso $ingreso)
    {
        return $this->service->generateCubiPdf($ingreso);
    }

    public function aprobarIngreso(Request $request)
    {
        DB::beginTransaction();

        try {
            $ingreso = $this->ingreso->findOrFail($request->ingreso_id);

            if ($ingreso->estado != 0) {
                return $this->respond(['success' => false, 'message' => message('MSG019')], 406);
            }

            $ubigeos = select_ubigeos_disponibles($ingreso->cajas);

            if (!count($ubigeos) > 0 || !(count($ubigeos) == $ingreso->cajas->count())) {
                return $this->respond(['success' => false, 'message' => message('MSG018')], 406);
            }

            $ingreso->update(['estado' => 1, 'fecha_aprobacion' => date('Y-m-d'), 'fecha_entrega' => $request->fecha_entrega]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }
        return $this->respond(['success' => true, 'message' => message('MSG003')]);
    }

    public function rechazarIngreso(Request $request)
    {
        DB::beginTransaction();

        try {
            $ingreso = $this->ingreso->findOrFail($request->ingreso_id);

            if ($ingreso->estado == 1 || $ingreso->estado == 2 || $ingreso->estado == 3) {
                return $this->respond(['success' => false, 'message' => message('MSG022')], 406);
            }

            $ingreso->update(['estado' => 3]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }
        return $this->respond(['success' => true, 'message' => message('MSG003')]);
    }

    public function ejecutarIngreso(Request $request)
    {
        DB::beginTransaction();

        try {
            $ingreso = $this->ingreso->findOrFail($request->ingreso_id);

            if ($ingreso->estado == 0 || $ingreso->estado == 2 || $ingreso->estado == 3) {
                return $this->respond(['success' => false, 'message' => message('MSG023')], 406);
            }

            $ubigeos = select_ubigeos_disponibles($ingreso->cajas);

            if (!count($ubigeos) > 0 || !(count($ubigeos) == $ingreso->cajas->count())) {
                return $this->respond(['success' => false, 'message' => message('MSG018')], 406);
            }

            $ingreso->estado = 2;
            $ingreso->save();

            foreach ($ingreso->cajas as $key => $item) {
                DB::table('caja_ingreso')->where(function($query) use ($ingreso, $key, $item) {
                    $query->where('ingreso_id', $ingreso->id)
                      ->where('caja_id', $item->id);
                })->update(['ubigeo_id' => $ubigeos[$key]->id, 'active' => 1]);

                DB::table('carpetas')->where(function($query) use ($item) {
                    $query->where('estado', 0)
                      ->where('caja_id', $item->id);
                })->update(['estado' => 1]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }
        return $this->respond(['success' => true, 'message' => message('MSG003')]);
    }

    public function ingresosPorAgenciaAdmin(Request $request)
    {
        $ingresos = $this->ingreso->ingresosPorAgencia($request->id)->orderBy('id', $request->sort)->paginate($request->per_page);
        return new IngresoAdminCollection($ingresos);
    }

    public function ingresosPorAgencia(Request $request)
    {
        // $ingresos = $this->ingreso->ingresosPorAgencia(get_user_agencia())->orderBy('id', $request->sort)->paginate($request->per_page);
        // return new IngresoAgenciaCollection($ingresos);

        if ($request->filled('filter.filters')) {
            return new IngresoAgenciaCollection(IngresoSearch::apply($request, $this->ingreso));
        }

        $ingresos = IngresoSearch::checkSortFilter($request, $this->ingreso->newQuery());

        return new IngresoAgenciaCollection($ingresos->ingresosPorAgencia(get_user_agencia())->paginate($request->take)); 
    }

    public function verDetalleIngreso(Request $request)
    {
        $ingreso = $this->ingreso->findOrFail($request->id);
        return new IngresoDetalleResource($ingreso);
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
