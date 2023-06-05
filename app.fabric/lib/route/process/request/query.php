<?php

namespace App\Fabric\Route\Process\Request;

class Query
{
    private string $raw;
    private ?array $definition;
    public function __construct(string $raw, ?array $definitions)
    {
        $this->raw = $raw;
        $this->definition = $definitions;
    }

    public function raw(): string
    {
        return $this->raw;
    }

    public function definitions(): array
    {
        return $this->definition ?: [];
    }
}