<?php

namespace App\Filters\Types;

use Illuminate\Database\Eloquent\Builder;

class DoesNotContain implements TypeQuery
{
    public function search(Builder $builder, Array $data)
    {
    	if ($data['field'] == 'number') {
    		$data['value'] = ltrim($data['value'], '0');
    	}
    	
        return $builder->whereRaw("NOT MATCH ({$data['field']}) AGAINST (? IN BOOLEAN MODE)", fullTextWildcardsEnd($data['value']));
    }
}
