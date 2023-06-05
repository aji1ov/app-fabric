<?php

namespace App\Fabric\Schedule;

use App\Fabric\Job;

class JobTask extends Task
{
    private Job $job;

    public function __construct(Job $job)
    {
        $this->job = $job;
        parent::__construct();
    }

    public function handle()
    {
        //TODO handler errors
        $this->getLogger()->secure()->continue(function(){
            $this->getLogger()->debug("[".$this->getKey()."]Start exec job: ".get_class($this->job));

            Job::run($this->job);

            $this->getLogger()->debug("[".$this->getKey()."]Done");
        });
    }

    protected function getHandleKey(): string
    {
        return md5(serialize($this->job));
    }

    public function createJob(): Job
    {
        return $this->job;
    }
}