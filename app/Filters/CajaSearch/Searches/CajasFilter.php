<?php

namespace App\Filters\CajaSearch\Searches;

class CajasFilter extends Filter
{
    protected $filterKeys = [
        'gestion' => 'filterByYear',
    ];

    protected function filterByYear()
    {
        // $this->builder = $this->builder->where('location', 'like', '%' . $this->request->input('value') . '%');
        $this->builder = $this->builder->whereRaw("MATCH (gestion) AGAINST (? IN BOOLEAN MODE)" , fullTextWildcardsInitEnd($this->request->input('value')));
    }
}