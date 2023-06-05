<?php

namespace App\Fabric\System\Container;

use App\Fabric\Container;

class CommandContainer extends Container
{
    public function getName(): string
    {
        return 'command';
    }
}