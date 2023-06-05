<?php

namespace App\Fabric\System\Command\Schedule;

use App\Fabric\Kernel;
use App\Fabric\System\Command\Queue\RunCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExecCommand extends RunCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Kernel::run_scheduler();
        parent::execute($input, $output);
    }

    protected function getDescription(): string
    {
        return 'Collect schedule tasks & run queue worker';
    }
}
