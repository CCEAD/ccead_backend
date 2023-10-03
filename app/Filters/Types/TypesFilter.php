<?php

namespace App\Filters\Types;

use Illuminate\Database\Eloquent\Builder;
use App\Filters\Types\Custom;
use App\Filters\Types\Contains;
use App\Filters\Types\DoesNotContain;
use App\Filters\Types\StartSwitch;
use App\Filters\Types\LastSwitch;
use App\Filters\Types\IsEqualTo;
use App\Filters\Types\ItIsNotEqualTo;
use App\Filters\Types\BeforeOrEqualTo;
use App\Filters\Types\AfterOrEqualTo;

class TypesFilter
{
    protected $types = [
        'custom' => Custom::class,
        'contains' => Contains::class,
        'doesnotcontain' => DoesNotContain::class,
        'startswitch' => StartSwitch::class,
        'endswitch' => LastSwitch::class,
        'qe' => IsEqualTo::class,
        'neq' => ItIsNotEqualTo::class,
        'lte' => BeforeOrEqualTo::class,
        'gte' => AfterOrEqualTo::class,
    ];

    public function run(Builder $builder, Array $data)
    {
        $type = new $this->types[$data['operator']];
        return $type->search($builder, $data);
    }
}