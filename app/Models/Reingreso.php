<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reingreso extends Model
{
    use SoftDeletes, HasFactory;

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    protected $fillable = [
        'fecha',
        'observacion',
        'agencia_id',
        'salida_id',
    ];

    public function cajas()
    {
        return $this->belongsToMany(Caja::class);
    }

    public function agencia()
    {
        return $this->belongsTo(Agencia::class);
    }

    public function salida()
    {
        return $this->belongsTo(Salida::class);
    }
}
