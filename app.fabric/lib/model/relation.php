<?php

namespace App\Fabric\Model;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Relation
{
    private string $reference_name;
    public function __construct(string $reference_name)
    {
        $this->reference_name = $reference_name;
    }

    public function getReferenceName(): string
    {
        return $this->reference_name;
    }
}