<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agencia extends Model
{
    use SoftDeletes, HasFactory;

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    protected $fillable = [
        'razon_social', 
        'nit', 
        'telefono', 
        'direccion', 
        'ciudad', 
        'poder_representacion', 
        'matricula_comercio', 
        'licencia_funcionamiento', 
        'representante_id', 
        'estado'
    ];

    public static function listAgencias()
    {
        return static::where('id', '!=', 1)->orderBy('id', 'DESC')->select('id', 'razon_social')->get();
    }

    public function scopeActiva($query)
    {
        return $query->where('estado', true)->where('id', '!=', 1);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function cajas()
    {
        return $this->hasMany(Caja::class);
    }

    public function salidas()
    {
        return $this->hasMany(Salida::class);
    }

    public function representante()
    {
        return $this->hasOne(Representante::class, 'id');
    }
}
