<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ubigeo extends Model
{
    use SoftDeletes, HasFactory;

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    protected $fillable = [
        'numero_interno', 
        'codigo'
    ];

    public function caja_ingreso()
    {
        return $this->hasMany(CajaIngreso::class, 'ubigeo_id');
    }
}
