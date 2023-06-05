<?php

namespace App\Fabric\Misc;

interface Decoder
{
    public function decode(string $raw);
}