<?php

namespace App\Fabric\Model;

use App\Fabric\Model\Query\Filter;

trait VTableSingleton
{
    public final static function getInstance(): static
    {
        $filter = new Filter();
        static::link($filter);
        return static::first(new Query($filter));
    }

    abstract public static function link(Filter $condition): string;
}