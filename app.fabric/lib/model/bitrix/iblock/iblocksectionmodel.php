<?php

namespace App\Fabric\Model\Bitrix\Iblock;

use App\Fabric\Model;
use App\Fabric\Model\Field;
use App\Fabric\Model\Primary;
use Bitrix\Main\Type\DateTime;

class IblockSectionModel extends Model\BitrixModel
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
    public int $left_margin;

    #[Field]
    public int $right_margin;

    public static function getOrmReference(): string
    {
        return \Bitrix\Iblock\SectionTable::class;
    }

    public static function getBOClass(): string
    {
        return \CIBlockSection::class;
    }

    /**
     * @return static[]
     */
    public function getSectionsChain(): array
    {
        return static::fetch(
            fn (Model\Query $query) =>
                $query->whereEquals('iblock_id', $this->iblock_id)
                    ->whereLess('left_margin', $this->left_margin)
                    ->whereMore('right_margin', $this->right_margin)
        );
    }
}