<?php

namespace App\Fabric\Schedule;

use App\Fabric\Kernel;

class CommandTask extends Task
{
    private string $command;

    public function __construct(string $command)
    {
        $this->command = $command;
        parent::__construct();
    }

    public function handle()
    {
        //TODO handler errors
        $this->getLogger()->secure()->continue(function(){
            $this->getLogger()->debug("[".$this->getKey()."]Start exec command: ".$this->command);
            $output = Kernel::command_run_string($this->command);
            $this->getLogger()->debug("[".$this->getKey()."]Done with result:\n".$output->fetch());
        });
    }

    protected function getHandleKey()
    {
        return md5($this->command);
    }
}