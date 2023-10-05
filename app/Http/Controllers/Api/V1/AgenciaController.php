<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Agencia;
use App\Models\User;
use App\Models\Representante;
use App\Http\Controllers\Api\V1\ApiController;
use Illuminate\Http\Request;
use App\Filters\AgenciaSearch\AgenciaSearch;
use App\Http\Requests\Agencia\StoreAgenciaRequest;
use App\Http\Requests\Agencia\RegistroAgenciaRequest;
use App\Http\Resources\Agencia\AgenciaResource;
use App\Http\Resources\Agencia\AgenciaCollection;
use App\Services\FileAgenciaService;
use Illuminate\Support\Facades\DB;
use App\Mail\RegistroUsuarioAdmin;
use Illuminate\Support\Facades\Mail;

class AgenciaController extends ApiController
{
    private $agencia;

    private $service;

    public function __construct(Agencia $agencia, FileAgenciaService $service)
    {
        $this->agencia = $agencia;
        $this->service = $service;
    }

    public function index(Request $request)
    {
        // $agencias = $this->agencia->where('id', '!=', 1)->orderBy('id', $request->sort)->paginate($request->per_page);
        // return new AgenciaCollection($agencias);

        if ($request->filled('filter.filters')) {
            return new AgenciaCollection(AgenciaSearch::apply($request, $this->agencia));
        }

        $agencias = AgenciaSearch::checkSortFilter($request, $this->agencia->newQuery());

        return new AgenciaCollection($agencias->activa()->paginate($request->take)); 
    }

    public function store(StoreAgenciaRequest $request)
    {
        return $this->respond($request->all());
    }

    public function registroAgencia(RegistroAgenciaRequest $request)
    {
        DB::beginTransaction();

        try {
            $name_poder_representacion = $this->service->getNameFile($request->archivos['poder_representacion']);
            $name_matricula_comercio = $this->service->getNameFile($request->archivos['matricula_comercio']);
            $name_licencia_funcionamiento = $this->service->getNameFile($request->archivos['licencia_funcionamiento']);

            $poder_representacion = $this->service->saveFile($request->archivos['poder_representacion'], $name_poder_representacion);
            $matricula_comercio = $this->service->saveFile($request->archivos['matricula_comercio'], $name_matricula_comercio);
            $licencia_funcionamiento = $this->service->saveFile($request->archivos['licencia_funcionamiento'], $name_licencia_funcionamiento);

            if ($poder_representacion && $matricula_comercio && $licencia_funcionamiento) {
                $representante = Representante::create([
                    'nombres' => $request->representante['nombres'], 
                    'apellidos' => $request->representante['apellidos'], 
                    'telefono' => $request->representante['telefono'], 
                    'correo' => $request->representante['correo'], 
                ]);

                $this->agencia->create([
                    'razon_social' => $request->agencia['razon_social'], 
                    'nit' => $request->agencia['nit'], 
                    'telefono' => $request->agencia['telefono'], 
                    'direccion' => $request->agencia['direccion'], 
                    'ciudad' => $request->agencia['ciudad'],
                    'poder_representacion' => $name_poder_representacion, 
                    'matricula_comercio' => $name_matricula_comercio, 
                    'licencia_funcionamiento' => $name_licencia_funcionamiento,
                    'representante_id' => $representante->id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }

        return $this->respondCreated();
    }

    public function altaAgencia(Request $request)
    {
        $agencia = $this->agencia->find($request->agencia);

        $pwd = gen_uuid();
        $user = User::create([
            'nombres' => $agencia->representante->nombres,
            'apellidos' => $agencia->representante->apellidos,
            'name' => strtolower(strtok($agencia->representante->nombres, " ") . $agencia->id),
            'email' => $agencia->representante->correo,
            'telefono' => $agencia->representante->telefono,
            'password' => $pwd,
            'temp_password' => $pwd,
            'agencia_id' => $agencia->id,
        ]);

        $user->assignRole('admin');

        $agencia->update(['estado' => true]);

        Mail::to($user->email)->send(new RegistroUsuarioAdmin($user));

        return $this->respondCreated();
    }

    public function lista()
    {
        $agencias = $this->agencia->listAgencias();
        return $this->respond($agencias);
    }
}
