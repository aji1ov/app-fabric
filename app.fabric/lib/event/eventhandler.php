<?php

namespace App\Fabric\Event;

class EventHandler
{
    private string $module_id;
    private string $event_name;
    private EventProvider $provider;

    public function __construct(string $module_id, string $event_name, EventProvider $provider)
    {
        $this->module_id = $module_id;
        $this->event_name = $event_name;
        $this->provider = $provider;
    }

    /**
     * @return EventProvider
     */
    public function getProvider(): EventProvider
    {
        return $this->provider;
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return $this->event_name;
    }

    /**
     * @return string
     */
    public function getModuleId(): string
    {
        return $this->module_id;
    }


}