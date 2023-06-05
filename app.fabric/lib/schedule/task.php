<?php

namespace App\Fabric\Schedule;

use App\Fabric\Job;
use App\Fabric\Logger\LoggerInterface;
use App\Fabric\System\Container\ScheduleContainer;
use Cron\CronExpression;

abstract class Task
{
    private ?CronExpression $cron;
    private $description = '';
    private $logger;

    public function __construct()
    {
        $this->logger = (new ScheduleContainer())->getLogger();
        $this->cron = null;
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function description(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function cron(string $expression): static
    {
        $this->cron = new CronExpression($expression);
        return $this;
    }

    public function getMask(): string
    {
        return implode(" ", $this->cron->getParts());
    }

    public function getKey(): ?string
    {
        if(!$this->cron) return null;
        return md5($this->getHandleKey().$this->cron->getExpression().$this->description);
    }

    public function getNextExecTime(int $now): int
    {
        return $this->cron->getNextRunDate($now)->getTimestamp();
    }

    public function createJob(): Job\ScheduleTaskExecutorJob
    {
        return new Job\ScheduleTaskExecutorJob($this->getKey());
    }

    abstract public function handle();
    abstract protected function getHandleKey();

}