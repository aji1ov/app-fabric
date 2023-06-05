<?php

namespace App\Fabric\Install\Spl;

class SystemEnv extends Env
{
    public function folder(): string
    {
        return realpath(__DIR__.'/../../../system')."/";
    }

    public function namespace(string $tail = ''): string
    {
        return 'App\Fabric\System\\'.$tail;
    }
}