<?php

namespace App\Filters\Types;

use Illuminate\Database\Eloquent\Builder;

class StartSwitch implements TypeQuery
{
    public function search(Builder $builder, Array $data)
    {
        if ($data['field'] == 'number') {
    		$data['value'] = ltrim($data['value'], '0');
    	}
    	
        return $builder->whereRaw("{$data['field']} LIKE ?", $data['value'].'%');
    }
}
