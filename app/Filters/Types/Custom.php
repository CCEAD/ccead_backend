<?php

namespace App\Filters\Types;

use Illuminate\Database\Eloquent\Builder;

class Custom implements TypeQuery
{
    public function search(Builder $builder, Array $data)
    {
    	return $builder;
    }
}
