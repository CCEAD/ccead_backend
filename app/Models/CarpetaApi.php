<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as MongoModel;

class CarpetaApi extends MongoModel
{
    protected $connection = 'mongodb';

    protected $collection = 'file';

    protected $fillable = [
        'uuid',
        'aduana',
        'gestion',
        'nro_declaracion',
        'nro_documento',
        'carpeta_id',
        'archivo_id',
    ];
}
