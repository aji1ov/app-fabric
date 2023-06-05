<?php

namespace App\Fabric\System\Container;

use App\Fabric\Container;

class ApiContainer extends Container
{
    public function getName(): string
    {
        return 'api';
    }
}