<?php

namespace App\Fabric\Model;

use App\Fabric\Model\Query\Filter;

trait VTable
{
    abstract public static function link(Filter $condition): string;
}