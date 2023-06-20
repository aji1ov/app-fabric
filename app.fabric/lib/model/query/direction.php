<?php

namespace App\Fabric\Model\Query;

enum Direction: string
{
    case ASC = 'ASC';
    case DESC = 'DESC';
    case RANDOM = 'RAND';
}