<?php

namespace App\Fabric\Model;

use App\Fabric\Model;
use App\Fabric\Model\Query\FilterEntity;
use App\Fabric\Model\Query\Operator;

class Selection
{
    /**
     * @var $modelClass Model
     */
    private string $modelClass;
    /**
     * @var FilterEntity[]
     */
    private array $filter = [];

    private int $limit = 0;
    private int $chunk = 0;

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    public function where(string $field, Operator $operator, mixed $value): static
    {
        $this->filter[] = new FilterEntity($operator, $field, $value);
        return $this;
    }

    public function whereEquals(string $field, mixed $value): static
    {
        return static::where($field, Operator::EQUALS, $value);
    }

    public function take(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function chunk(int $size): static
    {
        $this->chunk = $size;
        return $this;
    }

    public function first(): Model
    {
        return $this->modelClass::first($this);
    }

    /**
     * @return Model[]
     */
    public function fetch(): array
    {
        return $this->modelClass::fetch($this);
    }

}