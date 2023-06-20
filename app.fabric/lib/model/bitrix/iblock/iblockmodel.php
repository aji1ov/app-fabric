<?php

namespace App\Fabric\Model\Bitrix\Iblock;

use App\Fabric\Model;
use App\Fabric\Model\Field;
use App\Fabric\Model\Primary;
use App\Fabric\Model\Query;
use Bitrix\Main\Type\DateTime;

class IBlockModel extends Model\BitrixModel
{
    use Model\Bitrix\IBlockDriver;

    #[Field]
    public string $iblock_type_id;

    #[Field]
    public string $lid;

    #[Field]
    public bool $rest_on;

    #[Field]
    public ?string $api_code = null;

    #[Field]
    public ?string $description = null;

    #[Field]
    public ?string $description_type = null;

    #[Field]
    public bool $index_element;

    #[Field]
    public bool $index_section;

    #[Field]
    public bool $workflow;

    #[Field]
    public bool $bizproc;

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
    public string $tmp_id;

    public static function getOrmReference(): string
    {
        return \Bitrix\Iblock\IblockTable::class;
    }

    protected function fill(): void
    {
        $this->timestamp_x = new DateTime();
    }
}