<?php

namespace App\Fabric\System\Container;

use App\Fabric\Container;

class JobContainer extends Container
{
    public function getName(): string
    {
        return 'job';
    }
}