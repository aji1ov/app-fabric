<?php

namespace App\Fabric\Model;

use App\Fabric\Model;

class MapRelation
{
    private \ReflectionProperty $property;
    private Relation $relation;

    public function __construct(\ReflectionProperty $property, Relation $relation)
    {
        $this->property = $property;
        $this->relation = $relation;
    }

    public function getRelationName(): string
    {
        return $this->relation->getReferenceName();
    }

    public function getPrimaryField(): string
    {
        /** @var Model $reference_model */
        $reference_model = $this->property->getType()->getName();
        return $reference_model::map()->getPrimary()->getPropertyName();

    }

    public function getPropertyName(): string
    {
        return $this->property->getName();
    }

    /**
     * @return Model
     */
    public function getRelationModelClass(): string
    {
        return $this->property->getType()->getName();
    }

    public function getName(): string
    {
        return $this->property->getName();
    }

    public function getCode(): string
    {
        return 'relation_'.$this->getName();
    }

    public function create(Model $model, ValueSource $source): void
    {
        /** @var Model $relationClass */
        $relationClass = $this->getRelationModelClass();
        $relation = $relationClass::map()->restore($source);

        $this->property->setValue($model, $relation);
    }

    public function insert(Model $model, Model $relation): void
    {
        $this->property->setValue($model, $relation);
    }
}