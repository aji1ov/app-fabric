<?php

namespace App\Fabric\Model\Query;

enum JoinType: string
{
    case LEFT = 'left';
    case RIGHT = 'right';
    case INNER = 'inner';
}