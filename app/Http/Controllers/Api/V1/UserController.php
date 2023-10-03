<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Rol;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\User\UserRequest;
use App\Http\Requests\User\UsuarioPasswordRequest;
use App\Http\Resources\User\UserEditResource;
use App\Http\Resources\User\UserAdminCollection;
use App\Http\Resources\User\UserAgenciaCollection;
use Illuminate\Support\Facades\DB;

class UserController extends ApiController
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function listaUsuariosAdmin(Request $request)
    {
        $users = $this->user->activos()->orderBy('created_at', $request->sort)->paginate($request->per_page);
        return new UserAdminCollection($users);
    }

    public function listaUsuariosAgencia(Request $request)
    {
        $users = $this->user->usuariosPorAgencia(get_user_agencia())->activos()->orderBy('created_at', $request->sort)->paginate($request->per_page);
        return new UserAgenciaCollection($users);
    }

    public function store(UserRequest $request)
    {
        DB::beginTransaction();
        try {

            $user = $this->user->create([
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'name' => $request->name,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'password' => $request->password_confirmation,
                'agencia_id' => verificar_agencia() ? $request->agencia_id : getPermissionsTeamId(),
            ]);

            $rol = Rol::find($request->rol_id);

            // $user->assignRole($rol->name);

            DB::table('model_has_roles')->insert([
                'role_id' => $request->rol_id,
                'model_type' => 'App\Models\User',
                'model_id' => $user->id,
                'team_id' => get_user_agencia(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }
        return $this->respondCreated();
    }

    public function edit(User $user)
    {
        return new UserEditResource($user);
    }

    public function update(UserRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            $user->nombres = $request->nombres;
            $user->apellidos = $request->apellidos;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->telefono = $request->telefono;
            $user->agencia_id = verificar_agencia() ? $request->agencia_id : getPermissionsTeamId();

            $user->save();

            $rol = Rol::find($request->rol_id);

            $user->syncRoles([$rol->name]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }
        return $this->respondUpdated();
    }

    public function password(UsuarioPasswordRequest $request, User $user)
    {
        try {
            if ($user->id != auth()->user()->id) {
                return $this->respond(['success' => false, 'message' => message('MSG008')], 406);
            }

            $user->update(['password' => $request->password]);
        } catch (\Exception $e) {
            return $this->respondInternalError();
        }
        return $this->respondUpdated();
    }

    public function destroy(User $user)
    {
        try {
            if ($user->estado) {
                $user->update(['estado' => 0]);
            } else {
                $user->update(['estado' => 1]);
            }
        } catch (\Exception $e) {
            return $this->respondInternalError();
        }
        return $this->respondUpdated();
    }
}
