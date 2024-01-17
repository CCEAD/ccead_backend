<?php

namespace App\Filters\CarpetaSearch\Searches;

class CarpetasFilter extends Filter
{
    protected $filterKeys = [
        'nro' => 'filterByNro',
    ];

    protected function filterByNro()
    {
        $this->builder = $this->builder->select('carpetas.*')
        ->join('cajas', 'carpetas.caja_id', '=', 'cajas.id')
        ->where('cajas.agencia_id', getPermissionsTeamId())
        ->where('cajas.digitalizado', 1)
        ->whereRaw("MATCH (nro_declaracion, nro_registro) AGAINST (? IN BOOLEAN MODE)" , fullTextWildcardsInitEnd($this->request->input('nro')));
    }
}