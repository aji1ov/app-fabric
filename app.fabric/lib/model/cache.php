<?php

namespace App\Fabric\Model;

class Cache
{
    const NEVER = -1;
    const ALWAYS = PHP_INT_MAX;

    private function __construct(int $seconds)
    {

    }

    public static function ttl(int $seconds): Cache
    {
        return new Cache($seconds);
    }

    public static function never(): Cache
    {
        return static::ttl(Cache::NEVER);
    }
}