<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CajaIngreso extends Pivot
{
    public $incrementing = true;

    public $timestamps = false;

    protected $table = 'caja_ingreso';

    protected $fillable = [
        'ingreso_id',
        'caja_id',
        'ubigeo_id',
        'active',
    ];

    public function ubigeo()
    {
        return $this->belongsTo(Ubigeo::class, 'ubigeo_id');
    }
}
