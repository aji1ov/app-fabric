<?php

namespace App\Fabric;

use App\Fabric\Job\Queue;
use App\Fabric\Logger\LoggerOutput;
use App\Fabric\System\Container\JobContainer;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Job
{
    public static function run(Job $job, OutputInterface $output = null): void
    {
        if(!$output)
        {
            $container = new JobContainer();
            $output = new LoggerOutput($container->getLogger());
        }
        $job->exec($output); //TODO secure from exceptions
    }

    public final function delay(int $seconds = 0): void
    {
        Queue::dump($this, $seconds);
    }

    public final function dump(): void
    {
        $this->delay();
    }

    abstract protected function exec(OutputInterface $output);
}