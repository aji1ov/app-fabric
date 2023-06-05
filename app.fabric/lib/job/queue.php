<?php

namespace App\Fabric\Job;

use App\Fabric\Job;
use App\Fabric\Runtime\Session;
use App\Fabric\Registry\QueueTable;

class Queue extends Session
{
    public function __construct()
    {
        parent::__construct(static::class);
    }

    public static function dump(Job $job, int $delay = 0)
    {
        QueueTable::dump(serialize($job), time() + $delay);
    }

    public function load(): ?Job
    {
        $job = null;
        $this->wait(function() use (&$job){
            if($data = QueueTable::load())
            {
                $job = unserialize($data);
            }
        });

        return $job;
    }
}