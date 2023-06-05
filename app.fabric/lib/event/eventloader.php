<?php

namespace App\Fabric\Event;

use App\Fabric\Install\Spl;
use Bitrix\Main\EventManager;

class EventLoader
{
    private static ?EventLoader $instance = null;
    private static array $registryMap = [
        'Event\\Module\\' => ModuleEventRegistry::class
    ];

    public final static function getInstance(): static
    {
        if(!static::$instance)
        {
            static::$instance = new EventLoader();
        }

        return static::$instance;
    }

    private function __construct()
    {
        $this->provide();
    }

    private function provide(): void
    {
        Spl::load(Spl::path()->custom()->folder().'Event/*.php');
        foreach(static::$registryMap as $namespace => $baseClass)
        {
            $classes = Spl::filter(Spl::path()->custom()->namespace($namespace));
            foreach($classes as $class)
            {
                if(is_a($class, $baseClass, true))
                {
                    /** @var EventRegistry $registry */
                    $registry = new $class();
                    foreach($registry->getHandlers() as $handler)
                    {
                        $this->dispatch($handler);
                    }
                }
            }
        }
    }

    public function dispatch(EventHandler $handler): void
    {
        EventManager::getInstance()->addEventHandler($handler->getModuleId(), $handler->getEventName(), $handler->getProvider());
    }
}