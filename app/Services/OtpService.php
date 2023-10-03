<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OtpService
{
    public function generateOtp(User $user)
    {
        $codigo = Otp::where('usuario_id', $user->id)->latest()->first();

        $now = Carbon::now();

        if($codigo && $now->isBefore($codigo->expire_at)){
            return $codigo;
        }

        return Otp::create([
            'otp' => rand(202213, 999999),
            'usuario_id' => $user->id,
            'expire_at' => Carbon::now()->addMinutes(5)
        ]);
    }

    public function checkOtp(Request $request)
    {
        $codigo = Otp::where('usuario_id', $request->user)->where('otp', $request->otp)->first();

        $now = Carbon::now();

        if (!$codigo) {
            return response()->json([
                'success' => false,
                'message' => message('MSG012'),
            ], 401); 

        } elseif($codigo && $now->isAfter($codigo->expire_at)) {
            return response()->json([
                'success' => false,
                'message' => message('MSG013'),
            ], 401); 
        }

        return response()->json([
            'success' => true,
        ], 200); 
    }
}
