<?php

namespace App\Fabric\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Option
{
    private string $name;
    private OptionMode $mode = OptionMode::NODE;
    private ?string $shortcut;
    private string $description = '';
    private $value;
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function mode(OptionMode $mode): static
    {
        $this->mode = $mode;
        return $this;
    }

    public function shortcut(string $shortcut): static
    {
        $this->shortcut = $shortcut;
        return $this;
    }

    public function required(): static
    {
        $this->mode = OptionMode::REQUIRED;
        return $this;
    }

    public function optional(): static
    {
        $this->mode = OptionMode::OPTIONAL;
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

    public function getInputOption(): InputOption
    {
        return new InputOption($this->name, $this->shortcut, $this->mode->value, $this->description, $this->value);
    }
}