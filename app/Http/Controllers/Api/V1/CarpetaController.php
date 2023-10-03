<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Caja;
use App\Models\Carpeta;
use App\Http\Controllers\Api\V1\ApiController;
use Illuminate\Http\Request;
use App\Http\Requests\Carpeta\StoreCarpetaRequest;
use App\Http\Requests\Carpeta\UpdateCarpetaRequest;
use App\Http\Resources\Carpeta\CarpetaResource;
use App\Http\Resources\Carpeta\CarpetaCollection;
use App\Http\Resources\Carpeta\CarpetaAdminCollection;
use App\Services\CarpetaService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Imports\CarpetasImport;
use Maatwebsite\Excel\Facades\Excel;

class CarpetaController extends ApiController
{
    private $carpeta;
    private $service;

    public function __construct(Carpeta $carpeta, CarpetaService $service)
    {
        $this->carpeta = $carpeta;
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $carpetas = $this->carpeta->orderBy('created_at', $request->sort)->paginate($request->per_page);
        return new CarpetaCollection($carpetas);
    }

    public function store(StoreCarpetaRequest $request)
    {
        DB::beginTransaction();

        try {
            $datos = $request->all();
            $carpeta = $this->carpeta->create($datos);

            $carpeta->codigo = $this->generarCodigo($carpeta->caja_id, $carpeta->id);
            $carpeta->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respond($e);
        }

        return $this->respondCreated($carpeta);
    }

    public function show(Carpeta $carpeta)
    {
        return new CarpetaResource($carpeta);
    }

    private function generarCodigo($caja_id, $idCarpeta) {
        $caja = Caja::query()->where('id','=',$caja_id)->first();
        return $caja->agencia_id."-".$caja->id."-".$idCarpeta."-".$caja->gestion."R";
    }

    public function update(UpdateCarpetaRequest $request, Carpeta $carpeta)
    {
        DB::beginTransaction();

        try {
            if ($carpeta->estado >= 1) {
                return response()->json(['message' => message('MSG025'), 'success' => false], 406);
            }
            $carpeta->update($request->all());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }

        return $this->respondUpdated($request->all());
    }

    public function destroy(Carpeta $carpeta)
    {
        DB::beginTransaction();
        try {
            if ($carpeta->estado >= 1) {
                return response()->json(['message' => message('MSG026'), 'success' => false], 406);
            }
            $carpeta->delete();
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }
        return $this->respondDeleted();
    }

    public function carpetasPorCajaAdmin(Request $request)
    {
        $carpetas = $this->carpeta->carpetasPorCaja($request->id)->get();
        return new CarpetaAdminCollection($carpetas);
    }

    public function carpetasPorCaja(Request $request)
    {
        $carpetas = $this->carpeta->carpetasPorCaja($request->id)->get();
        return new CarpetaCollection($carpetas);
    }

    public function carpetasActivasPorCaja(Request $request)
    {
        $carpetas = $this->carpeta->carpetasPorCaja($request->id)->activa()->get();
        return new CarpetaCollection($carpetas);
    }

    public function escanerCarpeta(Request $request)
    {
        DB::beginTransaction();
        try {
            // /root/ccead/backend_ccead/app/Http/Utils/scrap.py

            $script = config('services.python.scraping');

            $http = trim($request->url, 'http://');

            $url = json_encode($http);

            //actualizar python
            $result = shell_exec("python3 $script $url");

            $resultData = json_decode($result, false);

            $collection = collect($resultData);
        
            $filtered = $collection->map(function ($value) {
                return str_replace(array("\r", "\n", ":"), '',$value);
            })->filter(function ($value) {
                return !empty($value);
            })->flatten();

            $map = $filtered->map(function ($value) {
                return trim(preg_replace('/[\t\n\r\s]+/', ' ', $value));
            })->filter(function ($value) {
                return !empty($value);
            })->flatten();

            $aduana = DB::table('aduanas')->where('codigo', trim($map[5], 'Código de Aduana '))->first();

            $data = [
                'nro_declaracion' => trim($map[10], 'C '),
                'nro_registro' => $map[38],
                'fecha_aceptacion' => $map[11],
                'aduana_id' => $aduana->id,
                'regimen_aduanero' => $map[3],
                'modalidad_regimen' => 'GENERAL',
                'modalidad_despacho' => 'GENERAL',
                'importador' => trim($map[41], 'Identificación '),
                'declarante' => trim($map[66], 'Identificación '),
                'pais_exportacion' => trim($map[75], '|b').' - '.strtoupper($map[74]),
                'aduana_ingreso' => substr($map[127], 0, 3).'-'.strtoupper(substr($map[127], 4)),
                'total_nro_facturas' => '',
                'total_nro_items' => $map[32],
                'total_nro_bultos' => $map[35],
                'total_peso_bruto' => trim($map[52], ' Kg.'),
                'total_valor_fob' => $map[104],
            ];
        DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }

        return response()->json($data);
    }

    public function importarCarpetasExcel(Request $request)
    {
        $data = [
            'caja' => (int)$request->caja_id,
        ];

        Excel::import(new CarpetasImport($data), $request->file('file'));
    }
}
