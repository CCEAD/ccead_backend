<?php

namespace App\Filters\CajaSearch;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use App\Filters\Search;

class CajaSearch extends Search
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
        if ($request->url() == config('services.server.ur').'cajas/pendientes') {
            return $query->cajasPorAgencia(get_user_agencia())->pendiente()->get();
        }

        if ($request->url() == config('services.server.ur').'cajas/activas') {
            return $query->cajasPorAgencia(get_user_agencia())->activa()->get();
        }

        if (get_user_agencia() == 1) {
            return $query->cajasPorAgencia($request->id)->paginate($request->take); 
        } else {
            return $query->cajasPorAgencia(get_user_agencia())->paginate($request->take);
        }
    }
}