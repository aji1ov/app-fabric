<?php

namespace App\Fabric\Model\Bitrix\Iblock;

use App\Fabric\Model;
use App\Fabric\Model\Query;

class IBlockPropertyModel extends Model\BitrixModel
{
    #[Model\Primary]
    public int $id;

    #[Model\Field]
    public int $iblock_id;

    #[Model\Field]
    public string $name;

    #[Model\Field]
    public string $code;


    public static function getOrmReference(): string
    {
        return \Bitrix\Iblock\PropertyTable::class;
    }
}