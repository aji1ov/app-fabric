<?php

namespace App\Fabric\Route;

use App\Fabric\Route\Dto\Property;

abstract class Dto
{
    public function toArray(): array
    {
        $collection = [];
        $reflect_class = new \ReflectionClass(static::class);
        foreach($reflect_class->getProperties() as $reflect)
        {
            $name = $reflect->getName();
            $value = $reflect->getValue($this);

            $property = new Property($reflect);
            if($property->isPrimitive() || is_array($value))
            {
                $collection[$name] = $value;
            }
            else if(is_a($value, Dto::class))
            {
                $collection[$name] = $value->toArray();
            }
        }

        return $collection;
    }
}