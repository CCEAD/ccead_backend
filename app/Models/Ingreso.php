<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingreso extends Model
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

    public function scopeIn($query, $ingresos)
    {
        return $query->whereIn('id', $ingresos);
    }

    public function scopeIngresosPorAgencia($query, $agencia)
    {
        return $query->where('agencia_id', $agencia);
    }

    public function agencia()
    {
        return $this->belongsTo(Agencia::class);
    }

    public function cajas()
    {
        return $this->belongsToMany(Caja::class)->using(CajaIngreso::class)->withPivot('id', 'ubigeo_id', 'active');
    }

    public function carpetas() {
        return $this->belongsToMany(
            Carpeta::class,
            'caja_ingreso',
            'ingreso_id',
            'caja_id',
            'id',
            'caja_id'
        );
    }
}
