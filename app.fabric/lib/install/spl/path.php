<?php

namespace App\Fabric\Install\Spl;

class Path
{
    public function system(): SystemEnv
    {
        return new SystemEnv();
    }

    public function custom(): CustomEnv
    {
        return new CustomEnv();
    }
}