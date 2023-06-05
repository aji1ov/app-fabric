<?php

namespace App\Fabric\Install\Spl;

class CustomEnv extends Env
{
    public function folder(): string
    {
        return $_SERVER['DOCUMENT_ROOT'].'/local/facade/';
    }

    public function namespace(string $tail = ''): string
    {
        return 'App\Facade\\'.$tail;
    }
}