<?php

namespace App\Fabric\Model;

use App\Fabric\Model;

abstract class ReferenceLoader
{
    abstract public static function load(Model $model, string $name): ?ReferenceLoader;
}