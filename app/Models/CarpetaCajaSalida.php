<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CarpetaCajaSalida extends Pivot
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'carpeta_caja_salida';

    public function carpetas() {
        return $this->belongsTo(Carpeta::class, 'carpeta_id');
    }
}
