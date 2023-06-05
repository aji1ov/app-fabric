<?php

namespace App\Fabric\Route;

use App\Fabric\Route\Process\Request;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
abstract class Access
{
    abstract public function calculate(Request $request, string $property, string $type);
}