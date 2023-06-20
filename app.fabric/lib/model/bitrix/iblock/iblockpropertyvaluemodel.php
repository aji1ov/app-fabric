<?php

namespace App\Fabric\Model\Bitrix\Iblock;

use App\Fabric\Model;
use App\Fabric\Model\Query;

class IBlockPropertyValueModel extends Model\BitrixModel
{
    #[Model\Primary]
    public ?int $id = null;

    #[Model\Field]
    public string $value;

    #[Model\Field]
    public string $iblock_element_id;

    #[Model\Field]
    public ?string $iblock_property_id;

    #[Model\Relation('iblock_property_id')]
    public IBlockPropertyModel $property;

    #[Model\Relation('iblock_element_id')]
    public ?IBlockElementModel $element;

    public static function getOrmReference(): string
    {
        return \Bitrix\Iblock\ElementPropertyTable::class;
    }


    public function element():IBlockElementModel
    {
        return IBlockElementModel::first(fn (Query $query) => $query->whereEquals('id', $this->iblock_element_id));
    }
}