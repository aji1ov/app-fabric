<?php

namespace App\Fabric\Model\Bitrix\Iblock;

use App\Fabric\Model;
use App\Fabric\Model\Field;
use App\Fabric\Model\Primary;
use App\Fabric\Model\Query;
use Bitrix\Main\Type\DateTime;

class IBlockElementModel extends Model\BitrixModel
{
    use Model\Bitrix\BitrixOldWriter;

    #[Model\Field]
    public int $iblock_id;

    #[Model\Relation('iblock_id')]
    public IBlockModel $iblock;

    #[Primary]
    public int $id;

    #[Field]
    public DateTime $timestamp_x;

    #[Field]
    public ?string $code;

    #[Field]
    public string $name;

    #[Field]
    public bool $active;

    #[Field]
    public int $sort;

    #[Field]
    public ?string $list_page_url;

    #[Field]
    public ?string $detail_page_url;

    #[Field]
    public ?string $section_page_url;

    #[Field]
    public ?string $canonical_page_url;

    #[Field]
    public ?string $xml_id;

    #[Field]
    public ?string $tmp_id;

    #[Model\Reference]
    public Model\Bitrix\PropertyCollection $property_values;


    public static function getOrmReference(): string
    {
        return \Bitrix\Iblock\ElementTable::class;
    }

    public static function getBOClass(): string
    {
        return \CIBlockElement::class;
    }

    public static function queryJoinProperty(string $property_code, ?Query $parent = null): Query
    {
        $property = strtolower($property_code).'_property';
        $value = strtolower($property_code).'_value';

        $query = new Query();
        $query->join($value, IBlockPropertyValueModel::class)
            ->on('id', ':'.$value.'.iblock_element_id')
            ->on($value.'.iblock_property_id', ':'.$property.'.id');

        $query->join($property, IBlockPropertyModel::class)
            ->on('iblock_id', ':'.$property.'.iblock_id');

        $query->whereEquals($property.'.code', $property_code);
        $query->alias($property_code, $value.".value");

        if($parent)
        {
            $parent->extend($query);
            return $parent;
        }

        return $query;
    }
}