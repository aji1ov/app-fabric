<?php

namespace App\Fabric\Route\Process\Request;

use App\Fabric\Error\FabricException;
use App\Fabric\Misc\Decoder;

class Body
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

    /**
     * @throws FabricException
     */
    public function decode(string $decodeClass)
    {
        if(!is_a($decodeClass, Decoder::class, true))
            throw new FabricException("Class(".$decodeClass.") is not a Decoder");

        /** @var Decoder $decoder */
        $decoder = new $decodeClass();
        return $decoder->decode($this->raw());
    }
}