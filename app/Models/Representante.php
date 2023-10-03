<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Representante extends Model
{
    use SoftDeletes, HasFactory;

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    protected $fillable = [
        'nombres', 
        'apellidos', 
        'telefono', 
        'correo', 
        'estado'
    ];

    public function agencia()
    {
        return $this->belongsTo(Agencia::class);
    }
}
