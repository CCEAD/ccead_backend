<?php
namespace App\Filters\IngresoSearch\Searches;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class Filter
{
    protected $request;

    protected $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        $method = $this->filterKeys[$this->request->input('column')];

        $this->{$method}();

        return $this->builder;
    }
}