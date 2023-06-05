<?php

namespace App\Fabric\Route\Dto;

use App\Fabric\Error\FabricException;
use App\Fabric\Route\Dto;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Collection
{
    private string $dto;

    /**
     * @throws FabricException
     */
    public function __construct(string $dto)
    {
        if(!is_a($dto, Dto::class, true))
        {
            throw new FabricException("Class(".$dto.") is not a Dto");
        }
        $this->dto = $dto;
    }

    public function createDto(): Dto
    {
        return new ($this->dto);
    }
}