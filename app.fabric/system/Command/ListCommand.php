<?php

namespace App\Fabric\System\Command;

use App\Fabric\Command;
use App\Fabric\Command\Config;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends HelpCommand
{

    protected function getDescription(): string
    {
        return 'Get list of available command';
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Available commands:\n");
        $commands = Command::listOfCommands();

        $commandData = [];
        $maxOffset = 0;

        foreach($commands as $command)
        {
            /** @var Command $command_object */
            $command_object = new $command();
            $definition = $this->getDefinition($command_object);

            $head = $command_object->getCommandName().$this->explainShort($definition);
            $offset = strlen($head);
            if($offset > $maxOffset) $maxOffset = $offset;

            $commandData[$command] = [$head, $command_object->getDescription()];
        }

        foreach($commands as $command)
        {
            list($head, $desc) = $commandData[$command];
            $offset = $maxOffset - strlen($head);
            $output->writeln("  ".$head. str_repeat(" ", $offset)."\t - ". $desc);
        }
    }

    protected function configure(Config $config)
    {
        //$config->option('all')->shortcut('a')->description('Show all command');
        //$config->option('page')->shortcut('p')->description('Show page');
    }
}