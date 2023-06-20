<?php

namespace App\Fabric\Model\Query;

class Filter
{
    /**
     * @var FilterEntity[]
     */
    protected array $filter = [];

    public function add(FilterEntity $entity): static
    {
        $this->filter[] = $entity;
        return $this;
    }

    public function where(string $key, Operator $operator, mixed $value): static
    {
        return static::add(new FilterEntity($operator, $key, $value));
    }

    public function whereEquals(string $key, mixed $value): static
    {
        return static::where($key, Operator::STRICT_EQUALS, $value);
    }

    public function whereLess(string $key, mixed $value): static
    {
        return static::where($key, Operator::LESS, $value);
    }

    public function whereMore(string $key, mixed $value): static
    {
        return static::where($key, Operator::MORE, $value);
    }

    public function whereNotEquals(string $key, mixed $value): static
    {
        return static::where($key, Operator::NOT_EQUALS, $value);
    }

    public function group(FilterLogic $logic = FilterLogic::AND): SubFilter
    {
        $sub = new SubFilter($logic);
        $this->filter[] = new FilterEntity(Operator::INNER,'', $sub);
        return $sub;
    }

    /**
     * @return FilterEntity[]
     */
    public function getFilterEntities(): array
    {
        return $this->filter;
    }

    public function calculateReferenceValue(string $key): mixed
    {
        foreach($this->filter as $filterEntity)
        {
            if(strtolower($filterEntity->getKey()) === strtolower($key) && $filterEntity->getOperator() === Operator::EQUALS)
            {
                return $filterEntity->getValue();
            }
        }

        return null;
    }
}