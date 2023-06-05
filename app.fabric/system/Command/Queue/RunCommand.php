<?php

namespace App\Fabric\System\Command\Queue;

use App\Fabric\Command;
use App\Fabric\Command\Config;
use App\Fabric\Job;
use App\Fabric\Job\Queue;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = time();
        $seconds = intval($input->getOption('time'));
        $limit = intval($input->getOption('limit'));
        $nowait = $input->hasOption('nowait');

        $queue = new Queue();

        $finished = 0;
        while(time() < $start + $seconds)
        {
            if($job = $queue->load())
            {
                Job::run($job);
                $finished += 1;
            }
            else if($nowait)
            {
                break;
            }

            if($limit && $finished >= $limit) break;
            sleep(0.1);
        }
    }

    protected function configure(Config $config)
    {
        $config->option('time')->shortcut('t')->optional()->default(10)->description('Seconds of execute');
        $config->option('limit')->shortcut('l')->optional()->default(0)->description('Max jobs');
        $config->option('nowait')->shortcut('n')->optional()->default(false)->description('Exit if no jobs detected');
    }

    protected function getDescription(): string
    {
        return 'Run queue worker';
    }
}
