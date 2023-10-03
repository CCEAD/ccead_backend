<?php

namespace App\Filters\Types;

use Illuminate\Database\Eloquent\Builder;

class IsEqualTo implements TypeQuery
{
    public function search(Builder $builder, Array $data)
    {
    	if (strpos($data['field'], '_') || $data['field'] == 'condition' || $data['field'] == 'estado') {
			return $builder->where($data['field'], $data['value']);
		} else  {
    		if ($data['field'] == 'number') {
	    		$data['value'] = ltrim($data['value'], '0');
	    	}
    		return $builder->whereRaw("MATCH ({$data['field']}) AGAINST (? IN BOOLEAN MODE) AND {$data['field']} = ?" , array(fullTextWildcardsInitEnd($data['value']), $data['value']));
    	}
    }
}
