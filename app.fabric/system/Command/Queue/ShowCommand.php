<?php

namespace App\Fabric\System\Command\Queue;

use App\Fabric\Command;
use App\Fabric\Registry\QueueTable;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Job in queue: '.QueueTable::getCount()."\n");

        if($jobs = QueueTable::getFirst())
        {
            $output->writeln("Next jobs:\nDATE | OBJECT");
            foreach($jobs as $job)
            {
                $cls = unserialize($job['CALLABLE']);
                $output->writeln(date("j.m.Y H:i:s", $job['TIMESTAMP'])." | ".get_class($cls));
            }
        }

    }

    protected function getDescription(): string
    {
        return 'Show queue jobs';
    }
}
