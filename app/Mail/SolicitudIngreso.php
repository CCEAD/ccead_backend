<?php

namespace App\Mail;

use App\Models\Ingreso;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolicitudIngreso extends Mailable
{
    use Queueable, SerializesModels;

    public $ingreso;

    public function __construct(Ingreso $ingreso)
    {
        $this->ingreso = $ingreso;
    }

    public function build()
    {
        return $this->subject('Solicitud de Ingreso')->view('mails.solicitud-ingreso');
    }
}
