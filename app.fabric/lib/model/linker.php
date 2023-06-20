<?php

namespace App\Fabric\Model;

use App\Fabric\Model;

/**
 * @deprecated
 */
class Linker
{
    private Model $model;
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function read(array $data): void
    {
        $reflect_class = new \ReflectionClass($this->model::class);
        foreach($reflect_class->getProperties() as $property)
        {
            foreach($property->getAttributes() as $attribute)
            {
                if(is_a($attribute->getName(), Field::class, true))
                {
                    /** @var Field $instance */
                    $instance = $attribute->newInstance();
                    $value = $instance->getReferenceValue($property, $data);
                    $property->setValue($this->model, $value);
                }
            }
        }
    }
}