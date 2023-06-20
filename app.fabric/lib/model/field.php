<?php

namespace App\Fabric\Model;

use App\Fabric\Model;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Field
{
    public function __construct(){}

    public function getReferenceName(\ReflectionProperty $property): string
    {
        return strtoupper($property->getName());
    }

    protected function valueToType(string $name, string $type, mixed $value)
    {
        if($type === 'bool')
        {
            return in_array($value, ['y', 'Y', '1', 'true', 'TRUE']);
        }

        return $value;
    }

    public function getReferenceValue(\ReflectionProperty $property, ValueSource $source)
    {
        return $this->valueToType($property->getName(), $property->getType()->getName(), $source->get($this->getReferenceName($property)));
    }

    public function setModelValue(\ReflectionProperty $property, ValueSource $source, mixed $value): void
    {
        if($property->getType()->getName() === 'bool')
        {
            $value = $value ? 'Y' : 'N';
        }
        $source->set(static::getReferenceName($property), $value);
    }
}