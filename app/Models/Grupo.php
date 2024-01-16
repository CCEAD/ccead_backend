<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grupo extends Model
{
    use SoftDeletes, HasFactory;

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    protected $fillable = [
        'name',
    ];

    public static function listGrupos()
    {
        return static::orderBy('id', 'DESC')->selectRaw('id as value, name as text')->get();
    }
}
