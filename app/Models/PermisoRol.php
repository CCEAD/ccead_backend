<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PermisoRol extends Pivot
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'role_has_permissions';
}
