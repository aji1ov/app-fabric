<?php

namespace App\Fabric;

use App\Fabric\Schedule\TaskCollection;

class Schedule
{
    private static ?Schedule $instance = null;
    private TaskCollection $collection;
    private function __construct()
    {
        $this->collection = new TaskCollection();
    }

    public final static function getInstance(): static
    {
        if(!static::$instance)
        {
            static::$instance = new Schedule();
        }

        return static::$instance;
    }

    public function getCollection(): TaskCollection
    {
        return $this->collection;
    }

}