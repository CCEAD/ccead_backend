<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Carpeta extends Model
{
    use SoftDeletes, HasFactory;

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    protected $fillable = [
        'codigo',
        'uuid',
        'nro_declaracion',
        'nro_registro',
        'fecha_aceptacion',
        'regimen_aduanero',
        'modalidad_regimen',
        'modalidad_despacho',
        'importador',
        'declarante',
        'pais_exportacion',
        'aduana_ingreso',
        'total_nro_facturas',
        'total_nro_items',
        'total_nro_bultos',
        'total_peso_bruto',
        'total_valor_fob',
        'cantidad_documentos',
        'estado',
        'caja_id',
        'aduana_id',
        'sincronizado',
    ];

    public function setFechaAceptacionAttribute($value)
    {
        $this->attributes['fecha_aceptacion'] = $value.' 00:00:00';
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    public function scopeCarpetasPorCaja($query, $caja)
    {
        return $query->where('caja_id', $caja);
    }

    public function scopeActiva($query)
    {
        return $query->where('estado', true);
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function aduana()
    {
        return $this->belongsTo(Aduana::class);
    }
}
