<?php

namespace App\Filters\CajaSearch\Filters;

use Illuminate\Database\Eloquent\Builder;
use App\Filters\Types\TypesFilter;
use App\Filters\Filter;

class EstadoId implements Filter
{
    public static function apply(Builder $builder, Array $data)
    {
        $typeFilter = new TypesFilter();
        return $typeFilter->run($builder, $data);
    }
}