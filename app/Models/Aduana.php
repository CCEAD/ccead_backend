<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aduana extends Model
{
    use SoftDeletes, HasFactory;

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    protected $fillable = [
        'codigo',
        'descripcion',
    ];

    protected $appends = ['text'];

    public function getTextAttribute()
    {
        return "$this->codigo-$this->descripcion";
    }

    public function caja()
    {
        return $this->hasMany(Carpeta::class);
    }

    public static function listAduanas()
    {
        return static::orderBy('id', 'DESC')->select('id', 'codigo', 'descripcion')->get();
    }
}
