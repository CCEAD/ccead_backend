<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salida extends Model
{
    use SoftDeletes, HasFactory;

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    protected $fillable = [
        'codigo',
        'fecha_solicitud',
        'fecha_aprobacion',
        'fecha_entrega',
        'observacion',
        'estado',
        'agencia_id',
    ];

    public function scopeDesc($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    public function scopeIn($query, $salidas)
    {
        return $query->whereIn('id', $salidas);
    }

    public function scopeSalidasPorAgencia($query, $agencia)
    {
        return $query->where('agencia_id', $agencia);
    }

    public function agencia()
    {
        return $this->belongsTo(Agencia::class);
    }

    public function cajas()
    {
        return $this->belongsToMany(Caja::class)->using(CajaSalida::class)->withPivot('id');
    }

    public function carpetas() {
        return $this->belongsToMany(
            Carpeta::class,
            'caja_salida',
            'salida_id',
            'caja_id',
            'id',
            'caja_id'
        );
    }

    public function reingreso()
    {
        return $this->hasOne(Reingreso::class);
    }
}
