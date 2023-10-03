<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;

class SmsService
{
    public function sendSms(Otp $otp, User $user)
    {
        $sid = config('services.twilio.twilio_sid');
        $token = config('services.twilio.twilio_token');
        $client = new Client($sid, $token);

        $client->messages->create(
            '+591'.$user->telefono,
            [
                'from' => config('services.twilio.twilio_from'),
                'body' => 'Su código de seguridad es: '.$otp->otp,
            ]
        );
    }
}
