<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

interface Filter
{
    public static function apply(Builder $builder, Array $data);
}