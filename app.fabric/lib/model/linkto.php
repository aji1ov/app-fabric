<?php

namespace App\Fabric\Model;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
class LinkTo
{
    public function __construct(string $model_class)
    {
    }
}