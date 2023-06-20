<?php

namespace App\Fabric\Model\Query;

enum Mode
{
    case FIRST;
    case ALL;
    case COUNT;
    case EXPLAIN;
}