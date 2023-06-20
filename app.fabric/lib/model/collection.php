<?php

namespace App\Fabric\Model;

use App\Fabric\Model;
use ReturnTypeWillChange;

abstract class Collection extends Lazy implements \ArrayAccess, \Iterator
{
    #[ReturnTypeWillChange]
    public function offsetExists($offset): bool
    {
        return isset($this->pull()[$offset]);
    }

    #[ReturnTypeWillChange]
    public function offsetGet($offset) : ?Model
    {
        return $this->pull()[$offset] ?? null;
    }


    public function offsetSet(mixed $offset, mixed $value): void{}

    public function offsetUnset(mixed $offset): void{}
}