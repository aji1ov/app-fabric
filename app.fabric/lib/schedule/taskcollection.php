<?php

namespace App\Fabric\Schedule;

use App\Fabric\Install\Spl;
use App\Fabric\Job;

class TaskCollection implements Collection
{
    public function __construct()
    {
        $this->collect();
        $this->index();
    }

    private function collect(): void
    {
        $schedule = $this;
        include Spl::path()->custom()->folder().'schedule.php';
    }

    private function index(): void
    {
        foreach($this->tasks as $task)
        {
            $this->table[$task->getKey()] = $task;
        }
    }

    /**
     * @var Task[] array
     */
    private array $tasks = [];

    /**
     * @var Task[] array
     */
    private array $table = [];

    public final function job(Job $job): JobTask
    {
        $task = new JobTask($job);
        $this->tasks[] = $task;
        return $task;
    }

    public final function command(string $command): CommandTask
    {
        $task = new CommandTask($command);
        $this->tasks[] = $task;
        return $task;
    }

    public final function handler(callable $callback): Task
    {
        $task = new PhpTask($callback);
        $this->tasks[] = $task;
        return $task;
    }

    /**
     * @return Task[]
     */
    public function getTable(): array
    {
        return $this->table;
    }

    public function getTask(string $key): ?Task
    {
        if($task = $this->table[$key]) return $task;
        return null;
    }
}