<?php

namespace App\Fabric\System\Command\Schedule;

use App\Fabric\Command;
use App\Fabric\Kernel;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CollectCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Kernel::run_scheduler();
    }

    protected function getDescription(): string
    {
        return 'Collect schedule tasks';
    }
}
