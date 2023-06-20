<?php

namespace App\Fabric\Model\Query;

class FilterEntity
{
    private Operator $operator;
    private string $key;
    private mixed $value;
    public function __construct(Operator $operator, string $key, mixed $value)
    {
        $this->operator = $operator;
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return Operator
     */
    public function getOperator(): Operator
    {
        return $this->operator;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}