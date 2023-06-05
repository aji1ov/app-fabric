<?php

namespace App\Fabric\Job;

use App\Fabric\Job;
use App\Fabric\Schedule;
use Symfony\Component\Console\Output\OutputInterface;

class ScheduleTaskExecutorJob extends Job
{
    public string $schedule_id;
    public function __construct(string $schedule_id)
    {
        $this->schedule_id = $schedule_id;
    }

    protected function exec(OutputInterface $output)
    {
        if($task = Schedule::getInstance()->getCollection()->getTask($this->schedule_id))
        {
            $task->handle();
        }
    }
}