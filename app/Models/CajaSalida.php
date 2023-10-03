<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CajaSalida extends Pivot
{
    public $incrementing = true;

    public $timestamps = false;

    protected $table = 'caja_salida';
}
