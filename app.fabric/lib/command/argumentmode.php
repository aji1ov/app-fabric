<?php

namespace App\Fabric\Command;

enum ArgumentMode: int
{
    case REQUIRED = 1;
    case OPTIONAL = 2;
    case IS_ARRAY = 4;
}