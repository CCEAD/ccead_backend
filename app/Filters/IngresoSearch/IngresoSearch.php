<?php

namespace App\Filters\IngresoSearch;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use App\Filters\Search;

class IngresoSearch extends Search
{
	public static function checkSortFilter(Request $request, Builder $query)
    {
        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            return $query->orderBy($sort[0]['field'], $sort[0]['dir']);
              
        } else {
            return $query->orderBy('id', 'DESC');
        }
    }
    
    protected static function createFilterDecorator($name)
    {
        return __NAMESPACE__ . '\\Filters\\' . Str::studly($name);
    }

    protected static function getResults(Builder $query, Request $request)
    {
        if (get_user_agencia() == 1) {
            return $query->ingresosPorAgencia($request->id)->paginate($request->take);
        } else {
            return $query->ingresosPorAgencia(get_user_agencia())->paginate($request->take);
        }
    }
}