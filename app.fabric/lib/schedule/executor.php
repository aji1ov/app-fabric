<?php

namespace App\Fabric\Schedule;

use App\Fabric\Runtime\Session;
use App\Fabric\Schedule;
use App\Fabric\Registry\ScheduleTable;

class Executor extends Session
{
    private ?int $now;

    public function __construct(?int $now = null)
    {
        parent::__construct(static::class);
        if(!$now) $now = time();
        $this->now = $now;
    }

    protected function syncTaskRegistry()
    {
        $toDrop = [];
        $toCreate = [];

        foreach(Schedule::getInstance()->getCollection()->getTable() as $task_id => $task)
        {
            $toDrop[] = $task_id;
            $toCreate[$task_id] = $task->getNextExecTime($this->now);
        }

        ScheduleTable::dropOldTasks($toDrop);
        ScheduleTable::createNewTasks($toCreate);
    }

    protected function provider(callable $handler): void
    {
        $this->syncTaskRegistry();

        foreach(ScheduleTable::getActiveTasks($this->now) as $row)
        {
            /** @var Task $task */
            $task = Schedule::getInstance()->getCollection()->getTask($row['SCHEDULE_ID']);
            $handler($task);
            ScheduleTable::updateTaskTime($row['ID'], $task->getNextExecTime($this->now));
        }
    }
}