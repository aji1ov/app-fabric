<?php

namespace App\Fabric\Model;

use App\Fabric\Model;

class MapProperty
{
    private \ReflectionProperty $property;
    private Field $field;

    public function __construct(\ReflectionProperty $property, Field $field)
    {
        $this->property = $property;
        $this->field = $field;
    }

    public function insertValue(Model $model, ValueSource $source, bool $escape_nulls = false): void
    {
        $value = $this->field->getReferenceValue($this->property, $source);
        if(!$escape_nulls || $value !== null)
        {
            $this->property->setValue($model, $value);
        }
    }

    public function getReferenceValue(ValueSource $source)
    {
        return $this->field->getReferenceValue($this->property, $source);
    }

    public function getValue(Model $model)
    {
        return $this->property->getValue($model);
    }

    public function setValue(Model $model, $value)
    {
        $this->property->setValue($model, $value);
    }

    public function extractValue(Model $model, ValueSource $source): void
    {
        $this->field->setModelValue($this->property, $source, $this->property->getValue($model));
    }

    public function hasValue(ValueSource $source): bool
    {
        $value = $this->field->getReferenceValue($this->property, $source);
        return is_set($value);
    }

    public function getModelName(): string
    {
        return $this->field->getReferenceName($this->property);
    }

    public function getPropertyName(): string
    {
        return $this->property->getName();
    }
}