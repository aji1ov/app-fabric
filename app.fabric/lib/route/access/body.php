<?php

namespace App\Fabric\Route\Access;

use App\Fabric\Error\FabricException;
use App\Fabric\Misc\Decoder;
use App\Fabric\Route\Access;
use App\Fabric\Route\Dto;
use App\Fabric\Route\Process\Request;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Body extends Access
{
    private ?string $dto = null;
    private ?string $encoder = null;
    public function __construct(?string $encoder = null, ?string $dto = null)
    {
        if($encoder && !is_a($encoder, Decoder::class, true))
        {
            throw new FabricException("Class(".$encoder.") is not a Decoder");
        }
        $this->encoder = $encoder;
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

        $definitions = [];
        if($this->encoder)
        {
            /** @var Decoder $encoder */
            $encoder = new ($this->encoder);
            $definitions = $encoder->decode($request->body()->raw());
        }
        else
        {
            $definitions = $request->body()->definitions();
        }

        $builder = new Dto\Builder($object, $definitions);
        $builder->fill();

        return $object;
    }
}