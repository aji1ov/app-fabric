<?php

namespace App\Fabric\Command;

use Symfony\Component\Console\Input\InputArgument;

class Argument
{
    private string $name;
    private ArgumentMode $mode = ArgumentMode::OPTIONAL;
    private string $description = '';
    private $value;
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function mode(ArgumentMode $mode): static
    {
        $this->mode = $mode;
        return $this;
    }

    public function required(): static
    {
        $this->mode = ArgumentMode::REQUIRED;
        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function default($value): static
    {
        $this->value = $value;
        return $this;
    }

    public function getInputArgument(): InputArgument
    {
        return new InputArgument($this->name, $this->mode->value, $this->description, $this->value);
    }
}