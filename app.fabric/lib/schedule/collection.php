<?php

namespace App\Fabric\Schedule;

use App\Fabric\Job;

interface Collection
{
    public function job(Job $job): JobTask;
    public function command(string $command): CommandTask;
    public function handler(callable $callback): Task;
}