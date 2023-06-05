<?php

namespace App\Fabric\Route\Decode;

use App\Fabric\Misc\Decoder;

class Json implements Decoder
{
    public function decode(string $raw): ?array
    {
        return json_decode($raw, true);
    }
}