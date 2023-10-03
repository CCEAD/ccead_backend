<?php

namespace App\Transformers;
use Carbon\Carbon;

class CarpetaTransformer extends Transformer
{
    protected $resourceName = 'carpeta';

    public function transform($data)
    {
        return [
            'id' => $data['id'],
            'nro_declaracion' => $data['nro_declaracion'],
            'nro_registro' => $data['nro_registro'],
        ];
    }
}

