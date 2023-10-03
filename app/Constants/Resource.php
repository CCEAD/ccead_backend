<?php

namespace App\Constants;

use MyCLabs\Enum\Enum;

class Resource extends Enum
{
    public const UBIGEO_INDEX = 'ubigeo.index';
    public const UBIGEO_SHOW = 'ubigeo.show';
    public const UBIGEO_CREATE = 'ubigeo.create';
    public const UBIGEO_UPDATE = 'ubigeo.update';
    public const UBIGEO_DELETE = 'ubigeo.delete';

    public const CAJA_INDEX = 'caja.index';
    public const CAJA_SHOW = 'caja.show';
    public const CAJA_CREATE = 'caja.create';
    public const CAJA_UPDATE = 'caja.update';
    public const CAJA_DELETE = 'caja.delete';

    public static function supported(): array
    {
        return collect(static::toArray())->values()->toArray();
    }
}