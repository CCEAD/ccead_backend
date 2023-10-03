<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, HasFactory, Notifiable;

    protected $guard_name = 'api';

    protected $fillable = [
        'nombres',
        'apellidos',
        'name',
        'email',
        'telefono',
        'estado',
        'password',
        'temp_password',
        'agencia_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeUsuariosPorAgencia($query, $agencia)
    {
        return $query->where('agencia_id', $agencia);
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function agencia()
    {
        return $this->belongsTo(Agencia::class);
    }

    public function roles() {
        return $this->belongsToMany(
            Rol::class,
            'model_has_roles',
            'model_id',
            'role_id',
            'id',
            'id'
        );
    }
}
