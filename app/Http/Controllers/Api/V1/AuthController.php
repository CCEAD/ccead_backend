<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\PasswordRegisterRequest;
use App\Services\OtpService;
use App\Services\PassportService;
use App\Services\SmsService;
use App\Mail\SolicitudCambioContraseña;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;


class AuthController extends ApiController
{
    private $service;
    private $passport;

    public function __construct(OtpService $service, PassportService $passport)
    {
        $this->service = $service;
        $this->passport = $passport;
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->username)
            ->orWhere('name', $request->username)
            ->first();

            if (!$user) {
                return response()->json([
                    'message' => message('MSG005'),
                ], 422);
            }

            if(!is_null($user->temp_password)) {
                if ($request->password == $user->temp_password) {
                    return $this->respond(['user' => ['id' => $user->id, 'numero' => mask_telefono($user->telefono)], 'actived' => true]);
                }

                return response()->json([
                    'message' => message('MSG004'),
                ], 422);
            }

            if ($user->estado === 0) {
                return response()->json([
                    'message' => message('MSG005'),
                ], 422);
            }

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => message('MSG004'),
                ], 422);
            }

            $data = $this->passport->generateToken($request, $user);

            return $this->respond($data);
        } catch (Exception $e) {
            return $this->respondInternalError();
        }
    }

    public function userVerification(Request $request)
    {
        DB::beginTransaction();

        try {
            $usuario = User::find($request->user);

            $codigo = $this->service->generateOtp($usuario);

            $message = new SmsService();

            $message->sendSms($codigo, $usuario);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }

        return $this->respond(['success' => true, 'message' => message('MSG011')]);
    }

    public function otpVerification(Request $request)
    {
        DB::beginTransaction();

        try {
            return $this->service->checkOtp($request);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }
    }

    public function passwordRegister(PasswordRegisterRequest $request)
    {
        DB::beginTransaction();

        try {
            $check = $this->service->checkOtp($request);

            if (!$check->getData()->success) {
                return $this->service->checkOtp($request);
            }

            $usuario = User::find($request->user);

            $usuario->password = $request->password;
            $usuario->temp_password = NULL;

            $usuario->save();

            $data = $this->passport->generateToken($request, $usuario);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }

        return $this->respond($data);
    }

    public function forgotPassword(Request $request)
    {
        DB::beginTransaction();

        try {
            $usuario = User::where('email', $request->email)->first();

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => message('MSG014'),
                ], 422);
            }

            $codigo = $this->service->generateOtp($usuario);

            Mail::to($usuario->email)->send(new SolicitudCambioContraseña($codigo));

            $message = new SmsService();

            $message->sendSms($codigo, $usuario);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }

        return $this->respond(['user' => $usuario->id, 'success' => true, 'message' => message('MSG011')]);
    }

    public function resetPassword(PasswordRegisterRequest $request)
    {
        DB::beginTransaction();

        try {
            $check = $this->service->checkOtp($request);

            if (!$check->getData()->success) {
                return $this->service->checkOtp($request);
            }

            $usuario = User::find($request->user);

            $usuario->password = $request->password;
            $usuario->save();

            $data = $this->passport->generateToken($request, $usuario);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }

        return $this->respond($data);
    }

    public function permisos() 
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $acl = ['*'];
        } else {
            $acl = $user->getPermissionsViaRoles()->map(function ($item, $key) {
                return $item->name;
            });
        }
        
        return $acl;
    }
    
    public function logout()
    {
        $accessToken = auth()->user()->token();

        $refreshToken = DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update([
                'revoked' => true,
            ]);

        $accessToken->revoke();

        return $this->respond(['status' => 200]);
    }
}
