<?php

namespace App\Fabric\Event;

use App\Fabric\Install\Spl;

abstract class ModuleEventRegistry extends EventRegistry
{

    private function getModuleId(): string
    {
        $module = explode("\\", str_replace(Spl::path()->custom()->namespace("Event\\Module\\"),"", static::class))[0];
        return strtolower($module);
    }

    /**
     * @return EventHandler[]
     */
    public function getHandlers(): array
    {
        $module_id = $this->getModuleId();
        $events = [];

        $rclass = new \ReflectionClass(static::class);
        foreach($rclass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
        {
            if($method->class === static::class)
            {
                $name = $method->name;
                $callback = [$this, $name];

                $events[] = new EventHandler($module_id, $name, new BitrixEventProvider($callback));
            }
        }

        return $events;
    }
}