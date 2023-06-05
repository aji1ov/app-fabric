<?php

namespace App\Fabric\Event;

abstract class EventRegistry
{
    /**
     * @return EventHandler[]
     */
    abstract public function getHandlers(): array;
}