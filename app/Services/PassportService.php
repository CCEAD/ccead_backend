<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class PassportService
{
    public function generateToken(Request $request, User $user)
    {
        try {
            $data = [
                'grant_type' => 'password',
                'client_id' => config('services.passport.client_id'),
                'client_secret' => config('services.passport.client_secret'),
                'username' => $user->email,
                'password' => $request->password,
            ];
    
            $req = Http::post(config('services.passport.login_endpoint'), $data);
    
            $res = $req->json();
    
            $permission = Http::acceptJson()->withToken($res['access_token'])->get(config('services.passport.permission'));

            $acl = $permission->json();
    
            $auth = [
                'id' => $user->id,
                'nombres' => $user->nombres,
                'apellidos' => $user->apellidos,
                'name' => $user->name,
                'email' => $user->email,
                'agencia' => $user->agencia->razon_social,
                'digital' => $user->agencia->digital == 0 ? false : true,
                'acl' => $acl
            ];
    
            return [
                'access_token'  => $res['access_token'],
                'refresh_token' => $res['refresh_token'],
                'expires_in' => $res['expires_in'],
                'user' => $auth
            ];
        } catch (Exception $e) {
            return $this->respondInternalError();
        }
    }
}
