<?php

namespace App\Fabric\Model\Bitrix;

trait ORM
{
    abstract public static function getOrmReference(): string;
}