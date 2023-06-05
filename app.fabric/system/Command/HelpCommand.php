<?php

namespace App\Fabric\System\Command;

use App\Fabric\Command;
use App\Fabric\Command\Config;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelpCommand extends Command
{

    protected function getDescription(): string
    {
        return 'Explain command arguments';
    }

    protected function configure(Config $config)
    {
        $config->argument('command')->description('Explain command');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if(!$input->hasArgument('command') || !$input->getArgument('command'))
        {
            $output->write(Command::parse('list', []));
        }
        else
        {
            if($command = static::tryClass($input->getArgument('command')))
            {
                $output->write($this->explainLong($command));
            }
            else
            {
                $output->writeln("Command `".$input->getArgument('command').'` not found');
            }
        }
    }

    protected function getDefinition(Command $command): InputDefinition
    {
        $definition = new InputDefinition();
        $config = new Config();
        $command->configure($config);
        $config->fill($definition);
        return $definition;
    }

    protected function explainLong(Command $command): string
    {
        $result = $command->getDescription()."\n";
        $definition = $this->getDefinition($command);

        if($definition->getArguments())
        {
            $result .= "\nArguments:\n";
            foreach ($definition->getArguments() as $argument)
            {
                $result .= $argument->getName().
                    ($argument->isRequired() ? " [REQUIRED]" : "").
                    ($argument->isArray() ? " [ARRAY]" : "").
                    ($argument->getDefault() !== null ? " [DEFAULT: ".$this->explain($argument->getDefault())."]" : "").
                    "\t - ".
                    $argument->getDescription()."\n";
            }
        }


        if($definition->getOptions())
        {
            $result .= "\nOptions:\n";
            foreach ($definition->getOptions() as $option)
            {
                $result .= "--".$option->getName().
                    ($option->getShortcut() ? ", -".$option->getShortcut() : "").
                    ($option->isValueRequired() ? " [REQUIRED]" : "").
                    ($option->isArray() ? " [ARRAY]" : "").
                    ($option->getDefault() !== null ? " [DEFAULT: ".$this->explain($option->getDefault())."]" : "").
                    "\t - ".
                    $option->getDescription()."\n";
            }
        }

        return $result;
    }

    protected function explainShort(InputDefinition $definition): string
    {
        $result = '';

        foreach ($definition->getOptions() as $option)
        {
            if(!$option->isValueRequired()) $result .= " [";
            else $result .= ' ';

            if($option->getShortcut()) $result .= "-".$option->getShortcut()."|";
            $result .= ($option->isArray() ? '...' : '')."--".$option->getName();
            if(!$option->isValueRequired()) $result .= "]";
        }

        foreach ($definition->getArguments() as $argument)
        {
            if(!$argument->isRequired()) $result .= " [";
            else $result .= ' ';
            $result .= ($argument->isArray() ? '...' : '').$argument->getName();
            if(!$argument->isRequired()) $result .= "]";
        }

        return $result;
    }

    protected function explain($value): bool|string
    {
        $result = $value;
        if(is_array($value))
        {
            $result = json_encode($value);
        }
        else if(is_string($value))
        {
            $result = "\"${value}\"";
        }
        else if(is_bool($value))
        {
            $result = $value ? 'true' : 'false';
        }
        else if(!isset($value))
        {
            $result = 'null';
        }

        return $result;
    }
}