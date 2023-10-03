<?php

namespace App\Filters\Types;

use Illuminate\Database\Eloquent\Builder;

class ItIsNotEqualTo implements TypeQuery
{
    public function search(Builder $builder, Array $data)
    {
    	if ($data['field'] == 'number') {
    		$data['value'] = ltrim($data['value'], '0');
    	}
    	
        return $builder->whereRaw("NOT MATCH ({$data['field']}) AGAINST (? IN BOOLEAN MODE) AND {$data['field']} != ?" , array($data['value'], $data['value']));
    }
}