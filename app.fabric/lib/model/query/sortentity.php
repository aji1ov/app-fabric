<?php

namespace App\Fabric\Model\Query;

class SortEntity
{
    private Direction $direction;
    private string $key;
    public function __construct(Direction $direction, string $key)
    {
        $this->direction = $direction;
        $this->key = $key;
    }

    /**
     * @return Direction
     */
    public function getDirection(): Direction
    {
        return $this->direction;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

}