<?php

namespace App\Fabric\Event;

class Emit
{
    private array $parameters = [];
    private ?Result $result = null;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function getParameter(string $key)
    {
        return $this->parameters[$key];
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameter(string $key, $value)
    {
        $this->parameters[$key] = $value;
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function setResult(Result $result)
    {
        $this->result = $result;
    }

    public function getResult(): ?Result
    {
        return $this->result;
    }
}