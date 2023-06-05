<?php

namespace App\Fabric\Install\Spl;

abstract class Env
{
    abstract public function folder(): string;
    abstract public function namespace(string $tail = ''): string;
}