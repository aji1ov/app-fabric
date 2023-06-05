<?php

namespace App\Fabric\System\Command\Schedule;

use App\Fabric\Command;
use App\Fabric\Kernel;
use App\Fabric\Registry\ScheduleTable;
use App\Fabric\Schedule;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {


        $defined_tasks = Schedule::getInstance()->getCollection()->getTable();
        $registered_tasks = ScheduleTable::getAllTasks();

        $print_defined_tasks = [];
        $print_unregistered_tasks = [];
        $print_deleted_tasks_count = 0;

        foreach($registered_tasks as $task_id => $next_exec)
        {
            if($task = $defined_tasks[$task_id])
            {
                $print_defined_tasks[] = $task_id." | ".$task->getMask()." | ".date("j.m.Y H:i:s", $next_exec)." | ".$task->getDescription();
            }
            else
            {
                $print_deleted_tasks_count += 1;
            }
        }

        foreach($defined_tasks as $task_id => $task)
        {
            if(!$registered_tasks[$task_id])
            {
                $print_unregistered_tasks[] = $task_id." | ".$task->getMask()." | ".$task->getDescription();
            }
        }

        $output->writeln('Defined tasks:');
        $output->writeln('ID | MASK | NEXT_EXECUTE | DESCRIPTION');
        $output->writeln(implode("\n", $print_defined_tasks));

        if($print_unregistered_tasks)
        {
            $output->writeln("\nStill unregistered tasks:");
            $output->writeln('ID  | MASK | DESCRIPTION');
            $output->writeln(implode("\n", $print_unregistered_tasks));
        }

        if($print_deleted_tasks_count)
        {
            $output->writeln("\nDeprecated tasks: ".$print_deleted_tasks_count);
        }

    }

    protected function getDescription(): string
    {
        return 'Show schedule registry';
    }
}
