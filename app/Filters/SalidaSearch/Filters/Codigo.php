<?php

namespace App\Filters\SalidaSearch\Filters;

use Illuminate\Database\Eloquent\Builder;
use App\Filters\Types\TypesFilter;
use App\Filters\Filter;

class Codigo implements Filter
{
    public static function apply(Builder $builder, Array $data)
    {
        $typeFilter = new TypesFilter();
        return $typeFilter->run($builder, $data);
    }
}