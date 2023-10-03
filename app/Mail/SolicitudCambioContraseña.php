<?php

namespace App\Mail;

use App\Models\Otp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolicitudCambioContraseña extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    public function __construct(Otp $otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('Código de Verificación')->view('mails.cambio-contraseña');
    }
}
