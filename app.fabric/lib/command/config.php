<?php

namespace App\Fabric\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class Config
{
    /**
     * @var Option[]
     */
    private array $options = [];
    /**
     * @var Argument[]
     */
    private array $arguments = [];
    public function __construct(){}

    public function argument(string $name): Argument
    {
        $argument = new Argument($name);
        $this->arguments[] = $argument;
        return $argument;
    }

    public function option(string $name): Option
    {
        $option = new Option($name);
        $this->options[] = $option;
        return $option;
    }

    public function fill(InputDefinition $definition): void
    {
        foreach($this->arguments as $argument)
        {
            $definition->addArgument($argument->getInputArgument());
        }

        foreach($this->options as $option)
        {
            $definition->addOption($option->getInputOption());
        }
    }
}