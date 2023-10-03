<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class Search
{
    public static function apply(Request $filters, Model $model)
    {
        $query = static::applyDecoratorsFromRequest($filters, $model->newQuery());
        return static::getResults($query, $filters);
    }

    private static function applyDecoratorsFromRequest(Request $request, Builder $query)
    {
        $query = static::checkSortFilter($request, $query);

        foreach ($request->input('filter.filters') as $key => $data) {

            $string = preg_replace('/.[^.]*$/', '', $data['field']);
            $data['field'] = $string ? $string.'_id' : $data['field']; 

            $decorator = static::createFilterDecorator($data['field']);

            if (static::isValidDecorator($decorator)) {
                $query = $decorator::apply($query, $data);
            }
        }
        return $query;
    }

    private static function isValidDecorator($decorator)
    {
        return class_exists($decorator);
    }
}