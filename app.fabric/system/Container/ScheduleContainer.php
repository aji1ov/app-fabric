<?php

namespace App\Fabric\System\Container;

use App\Fabric\Container;

class ScheduleContainer extends Container
{
    public function getName(): string
    {
        return 'schedule';
    }
}