<?php

namespace App\Filters\Types;

use Illuminate\Database\Eloquent\Builder;

interface TypeQuery
{
    public function search(Builder $builder, Array $data);
}