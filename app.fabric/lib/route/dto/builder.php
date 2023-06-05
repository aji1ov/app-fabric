<?php

namespace App\Fabric\Route\Dto;

use App\Fabric\Error\ApiException;
use App\Fabric\Error\DtoValidateException;
use App\Fabric\Route\Dto;
use App\Fabric\Error\FabricException;

class Builder
{
    private Dto $dto;
    private array $parameters;
    private array $depth;
    public function __construct(Dto $dto, array $parameters, array $depth = [])
    {
        $this->dto = $dto;
        $this->parameters = $parameters;
        $this->depth = $depth;
    }

    protected function getDepth(): array
    {
        return $this->depth;
    }

    /**
     * @throws ApiException
     * @throws FabricException
     */
    public function fill(): void
    {
        $reflect_class = new \ReflectionClass(get_class($this->dto));
        foreach($reflect_class->getProperties() as $reflect_property)
        {
            try
            {
                $name = $reflect_property->getName();
                $property = new Property($reflect_property);
                if(!$property->isPrimitive())
                {
                    $this->fillCollection($property, $reflect_property, $name);
                    continue;
                }

                $value = PrimitiveValidator::validity($property, $this->parameters[$name], in_array($name, array_keys($this->parameters)));
                if(isset($value))
                {
                    $reflect_property->setValue($this->dto, $value);
                }
            }
            catch (DtoValidateException $e)
            {
                $this->convertError($name, $e);
            }
        }
    }

    /**
     * @throws FabricException
     */
    private function fillCollection(Property $property, \ReflectionProperty $reflect, string $name): void
    {
        if(class_exists($property->getType()))
        {
            if(!is_a($property->getType(), Dto::class, true)) return;
            $dto = new ($property->getType());

            $builder = new Builder($dto, $this->parameters[$name], array_merge($this->depth, [$name]));
            $builder->fill();
            $reflect->setValue($this->dto, $dto);
            return;
        }

        foreach($reflect->getAttributes() as $attribute)
        {
            if(is_a($attribute->getName(), Collection::class, true))
            {
                /** @var Collection $instance */
                $instance = $attribute->newInstance();
                try
                {
                    $this->fillDtoCollection($instance, $reflect, $name);
                }
                catch (DtoValidateException $e)
                {
                    $this->convertError($name, $e, $e->append);
                }
                return;
            }
        }

        $reflect->setValue($this->dto, $this->parameters[$name]);
    }

    /**
     * @throws DtoValidateException
     * @throws FabricException
     */
    private function fillDtoCollection(Collection $instance, \ReflectionProperty $reflect, string $name): void
    {
        $result = [];
        foreach($this->parameters[$name] as $key => $parameters)
        {
            if(!is_array($parameters))
            {
                throw new DtoValidateException(
                    DtoValidateException::COMPATIBILITY,
                    PrimitiveValidator::ARRAY,
                    PrimitiveValidator::detectType($parameters, in_array($name, array_keys($this->parameters))),
                    $key
                );
            }

            $dto = $instance->createDto();
            $builder = new Builder($dto, $parameters, [$name."[".$key."]"]);
            $builder->fill();

            $result[$key] = $dto;
        }

        $reflect->setValue($this->dto, $result);
    }

    /**
     * @throws ApiException
     */
    private function convertError(string $name, DtoValidateException $e, ?string $append = null)
    {
        $path = implode(".", array_merge($this->depth, $append ? [$name."[".$append."]"] : [$name]));
        if($e->error_type === DtoValidateException::REQUIRED)
        {
            throw new ApiException("Argument `".$path."` is required", "dto_validity");
        }
        else if($e->error_type === DtoValidateException::NOT_NULL)
        {
            throw new ApiException("Argument `".$path."` can't be null", "dto_validity");
        }
        else
        {
            throw new ApiException("Argument `".$path."` must be \"".$e->waited."\" but \"".$e->given."\" given", "dto_validity");
        }
    }
}