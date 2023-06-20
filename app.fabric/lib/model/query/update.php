<?php

namespace App\Fabric\Model\Query;

use App\Fabric\Model;
use App\Fabric\Model\ValueSource;

class Update
{
    private Operation $operation;
    private ValueSource $source;
    private Model $model;
    public function __construct(Operation $operation, ValueSource $source, Model $model)
    {
        $this->operation = $operation;
        $this->source = $source;
        $this->model = $model;
    }

    /**
     * @return Operation
     */
    public function getOperation(): Operation
    {
        return $this->operation;
    }

    /**
     * @return ValueSource
     */
    public function getSource(): ValueSource
    {
        return $this->source;
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }
}