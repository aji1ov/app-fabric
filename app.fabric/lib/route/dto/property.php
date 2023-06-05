<?php

namespace App\Fabric\Route\Dto;

class Property
{
    protected bool $primitive = true;
    protected bool $required = false;
    protected bool $notnull = false;
    protected string $type;

    public function __construct(\ReflectionProperty $prop)
    {
        if(!$prop->getType()->allowsNull())
        {
            $this->notnull = true;
        }

        if(!isset($prop->getDeclaringClass()->getDefaultProperties()[$prop->name]))
        {
            $this->required = true;
        }

        if(!in_array($prop->getType()->getName(), ['int', 'float', 'string', 'bool']))
        {
            $this->primitive = false;
        }

        $this->type = $prop->getType()->getName();
    }

    public function isPrimitive(): bool
    {
        return $this->primitive;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isNotnull(): bool
    {
        return $this->notnull;
    }

    public function getType(): string
    {
        return $this->type;
    }
}