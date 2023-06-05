<?php

namespace App\Fabric\Route\Access;

use App\Fabric\Error\FabricException;
use App\Fabric\Route\Access;
use App\Fabric\Route\Dto;
use App\Fabric\Route\Dto\Builder;
use App\Fabric\Route\Process\Request;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Query extends Access
{
    private ?string $dto = null;
    public function __construct(?string $dto = null)
    {
        if($dto) $this->setDto($dto);
    }

    private function setDto(string $dto)
    {
        if(!is_a($dto, Dto::class, true))
        {
            throw new FabricException("Class(".$dto.") is not a Dto");
        }
        $this->dto = $dto;
    }

    public function calculate(Request $request, string $property, string $type)
    {
        if(!$this->dto) $this->setDto($type);
        $object = new ($this->dto);
        $builder = new Builder($object, $request->query()->definitions());
        $builder->fill();

        return $object;
    }
}