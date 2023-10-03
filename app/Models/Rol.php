<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use App\Traits\SecureDelete;

class Rol extends Role
{
    use SecureDelete, HasFactory;

    protected $fillable = [
        'name',
        'guard_name',
        'team_id',
    ];

    protected $relationships = [
        'users'
    ];

    public static function listRoles($agencia)
    {
        return static::orderBy('id', 'DESC')->where('team_id', $agencia)->select('id', 'name')->get();
    }

    public function agencia()
    {
        return $this->belongsTo(Agencia::class, 'team_id');
    }
}
