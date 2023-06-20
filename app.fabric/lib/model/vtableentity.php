<?php

namespace App\Fabric\Model;

use App\Fabric\Model;
use App\Fabric\Model\Query\Filter;

class VTableEntity
{
    private string $childModel;
    private string $parentModel;

    private Filter $condition;

    public function __construct(string $parentModel, string $childModel, Filter $condition)
    {
        $this->parentModel = $parentModel;
        $this->childModel = $childModel;
        $this->condition = $condition;
    }

    public function getParentModel(): string
    {
        return $this->parentModel;
    }

    protected function checkCondition($left, $right, Model\Query\Operator $operator): bool
    {
        switch ($operator)
        {
            case Model\Query\Operator::EQUALS:
                return $left == $right;
        }

        return false;
    }

    public function tryCreate(ValueSource $source): ?Model
    {
        /** @var Map $map */
        $map = $this->childModel::map();
        /** @var Model\Query\FilterEntity $filterEntity */
        foreach($this->condition->getFilterEntities() as $filterEntity)
        {
            $key = $filterEntity->getKey();
            $value = $map->getField($key)->getReferenceValue($source);

            if(!$this->checkCondition($value, $filterEntity->getValue(), $filterEntity->getOperator()))
            {
                return null;
            }
        }


        return new $this->childModel;
    }

    /**
     * @return Filter
     */
    public function getCondition(): Filter
    {
        return $this->condition;
    }

    /**
     * @return Model
     */
    public function getChildModel(): string
    {
        return $this->childModel;
    }
}