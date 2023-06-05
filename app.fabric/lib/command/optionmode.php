<?php

namespace App\Fabric\Command;

enum OptionMode: int
{
    case NODE = 1;
    case REQUIRED = 2;
    case OPTIONAL = 4;
    case IS_ARRAY = 8;
}