<?php

namespace App\Fabric\Route\Access;

use App\Fabric\Route\Access;
use App\Fabric\Route\Process\Request;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Definition extends Access
{
    private ?string $property;
    public function __construct(?string $property = null)
    {
        $this->property = $property;
    }

    public function calculate(Request $request, string $property, string $type)
    {
        return $request->definition($this->property ?: $property);
    }
}