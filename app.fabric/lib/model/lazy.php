<?php

namespace App\Fabric\Model;

use App\Fabric\Model;

abstract class Lazy
{
    private Model $model;
    private string $name;
    private mixed $cache = null;
    public function __construct(Model $model, string $name)
    {
        $this->model = $model;
        $this->name = $name;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Model[]
     */
    protected final function pull(): array
    {
        if(!isset($this->cache))
        {
            $this->cache = static::initialize();
        }

        return $this->cache;
    }

    protected final function reset(): void
    {
        $this->cache = null;
    }

    /**
     * @return Model[]
     */
    abstract protected function initialize(): array;
}