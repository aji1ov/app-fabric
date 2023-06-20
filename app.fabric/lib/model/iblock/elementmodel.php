<?php

namespace App\Fabric\Model\Iblock;

use App\Fabric\Model;
use App\Fabric\Model\Field;
use App\Fabric\Model\Relation;

abstract class ElementModel extends Model\BitrixModel
{

    #[Field]
    public int $iblock_id;

    public static function first(Query|callable|null $handler = null): static
    {
        $query = static::createQuery($handler);
        $query->once();
        static::findModel($query);

        $source = new Model\ValueSource(static::make($query)->GetNext());
        $record = new static();
        Model\Map::get(static::class)->restore($record, $source);
        return $record;
    }

    /**
     * @param Query|callable|null $handler
     * @return ElementModel[]
     */
    public static function fetch(Query|callable|null $handler = null): array
    {
        $query = static::createQuery($handler);
        static::findModel($query);

        $cresult = static::make($query);
        $result = [];
        while($data = $cresult->GetNext())
        {
            $record = new static();
            Model\Map::get(static::class)->restore($record, new Model\ValueSource($data));
            $result[] = $record;
        }

        return $result;
    }

    public static function primary(mixed $value): static
    {
        return static::first(fn (Query $query) => $query->filter(static::map()->getPrimary()->getModelName(), $value));
    }

    protected static function make(Query $query): \CIBlockResult
    {
        $limit = $query->getLimit();
        return \CIBlockElement::GetList(
            $query->getOrder(),
            $query->getFilter(),
            false,
            $limit ? ['nTopCount' => $limit] : false
        );
    }
}