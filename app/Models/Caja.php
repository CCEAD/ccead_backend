<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;
use App\Traits\SecureDelete;

class Caja extends Model
{
    use LogsActivity, SoftDeletes, SecureDelete, HasFactory;

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    protected $fillable = [
        'gestion', 
        'cod_interno', 
        'cod_almacen', 
        'cant_carpetas',
        'reg_inicial',
        'reg_final',
        'observaciones', 
        'agencia_id', 
        'estado',
    ];

    protected $relationships = [
        'carpetas'
    ];

    protected $appends = ['year'];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->cod_almacen = Str::uuid();
        });
    }

    public function scopeDesc($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    public function scopeIn($query, $cajas)
    {
        return $query->whereIn('id', $cajas);
    }

    public function getYearAttribute()
    {
        return "gestión-$this->gestion";
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    public function scopeCajasPorAgencia($query, $agencia)
    {
        return $query->where('agencia_id', $agencia);
    }

    public function scopeActiva($query)
    {
        return $query->where('estado_id', true);
    }

    public function scopePendiente($query)
    {
        return $query->where('estado_id', false);
    }

    public function agencia()
    {
        return $this->belongsTo(Agencia::class);
    }

    public function carpetas()
    {
        return $this->hasMany(Carpeta::class);
    }

    public function ingresos()
    {
        return $this->belongsToMany(Ingreso::class)->using(CajaIngreso::class)->withPivot('id', 'ubigeo_id', 'active');
    }

    public function salidas()
    {
        return $this->belongsToMany(Salida::class)->using(CajaSalida::class)->withPivot('id');
    }

    public function detalle_ingreso()
    {
        return $this->belongsToMany(
            Ubigeo::class,
            'caja_ingreso',
            'caja_id',
            'ubigeo_id',
            'id',
            'id'
        );
    }

    public function detalle_salida()
    {
        return $this->belongsToMany(
            CarpetaCajaSalida::class,
            'caja_salida',
            'caja_id',
            'id',
            'id',
            'caja_salida_id'
        );
    }

    public function detalle_reingreso()
    {
        return $this->belongsToMany(
            CarpetaCajaReingreso::class,
            'caja_reingreso',
            'caja_id',
            'id',
            'id',
            'caja_reingreso_id'
        );
    }
}
