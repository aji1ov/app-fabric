<?php

namespace App\Fabric\Model\Query;

enum Operator: string
{
    case EQUALS = '';
    case STRICT_EQUALS = '=';
    case MORE = '>';
    case MORE_OR_EQUALS = '>=';
    case LESS = '<';
    case LESS_OR_EQUALS = '<=';
    case NOT_EQUALS = '<>';
    case INNER = '.';
}