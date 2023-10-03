<?php

namespace App\Filters\Types;

use Illuminate\Database\Eloquent\Builder;

class Contains implements TypeQuery
{
    public function search(Builder $builder, Array $data)
    {
    	if (strpos($data['value'], '-')) {
    		$data['value'] = str_replace("-", " ", $data['value']);
    	}

    	if ($data['field'] == 'number') {
    		$data['value'] = ltrim($data['value'], '0');
    	}

		// if ($data['field'] == 'location') {
		// 	return $builder->where('location', 'like', '%' . $data['value'] . '%');
    	// }

		// if ($data['field'] == 'location') {
		// 	return $builder->where('location', 'regexp', '[[:<:]]'.$data['value'].'[[:>:]]');
    	// }

        return $builder->whereRaw("MATCH ({$data['field']}) AGAINST (? IN BOOLEAN MODE)" , fullTextWildcardsInitEnd($data['value']));
    }
}

// $columns = implode(',',$this->searchable);
// explain SELECT *
// FROM products
// WHERE MATCH(title) AGAINST('Error' IN BOOLEAN MODE) AND MATCH(category) AGAINST('sint' IN BOOLEAN MODE) and date = "1988-03-12"

// EXPLAIN SELECT *
// FROM products
// WHERE price LIKE '2%' AND date = "2004-11-28" AND MATCH(category) AGAINST('quo' IN BOOLEAN MODE)

// SELECT * FROM products
// WHERE MATCH(title) AGAINST ('dol*' IN BOOLEAN MODE)
// HAVING title LIKE ('dol%');

// SELECT * FROM products
// WHERE MATCH(title) AGAINST ('+Dolores quos dolores molestiae similique.*' IN BOOLEAN MODE)
// HAVING title = "Dolores quos dolores molestiae similique.";

// SELECT * FROM products
// WHERE MATCH(name) AGAINST ('va*' IN BOOLEAN MODE)
// HAVING name LIKE 'va%';