<?php

namespace App\Fabric\Model\Bitrix;

use App\Fabric\Model;
use App\Fabric\Model\Bitrix\Iblock\IBlockElementModel;
use App\Fabric\Model\Bitrix\Iblock\IBlockPropertyValueModel;
use App\Fabric\Model\Query\JoinType;
use App\Fabric\Model\ReferenceLoader;

class Property extends Model\ReferenceLoader
{
    private ?IBlockPropertyValueModel $value;
    public function __construct(?IBlockPropertyValueModel $value)
    {
        $this->value = $value;
    }

    public function getModel(): ?IBlockPropertyValueModel
    {
        return $this->value;
    }

    /**
     * @param IBlockElementModel $model
     * @return ReferenceLoader|null
     */
    public static function load(Model $model, string $name): ?ReferenceLoader
    {
        return new Property($model->properties->getValue(strtoupper($name)));
    }
}