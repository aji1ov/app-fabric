<?php

namespace App\Fabric\Model\Iblock;

use App\Fabric\Model;
use App\Fabric\Model\BitrixModel;

abstract class IblockModel extends Model\BitrixModel
{
    private static $load_cache = [];

    public static final function load(): static
    {
        if(!static::$load_cache[static::class])
        {
            $query = new Query();
            static::findModel($query);

            $record = new static();
            $source = new Model\ValueSource(static::make($query)->GetNext());
            Model\Map::get(static::class)->restore($record, $source);

            static::$load_cache[static::class] = $record;
        }

        return static::$load_cache[static::class];
    }

    protected static function make(Query $query): \CDBResult
    {
        return \CIBlock::GetList($query->getOrder(), $query->getFilter());
    }
}