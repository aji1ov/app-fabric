<?php

namespace App\Fabric\Model;

use App\Fabric\Model;

class MapReference
{
    private string $lazy;
    private \ReflectionProperty $property;
    public function __construct(string $lazy, \ReflectionProperty $property)
    {
        $this->lazy = $lazy;
        $this->property = $property;
    }

    public function create(Model $model, string $name): void
    {
        $reference = new $this->lazy($model, $name);
        $this->property->setValue($model, $reference);
    }
}