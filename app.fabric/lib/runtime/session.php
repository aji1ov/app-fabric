<?php

namespace App\Fabric\Runtime;

use Symfony\Component\Console\Command\LockableTrait;

class Session
{
    use LockableTrait;

    private string $name;
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function nowait(callable $handler): void
    {
        $this->lock($this->name);
        $this->provider($handler);
        $this->release();
    }

    public function wait(callable $handler): void
    {
        $this->lock($this->name, true);
        $this->provider($handler);
        $this->release();
    }

    protected function provider(callable $handler): void
    {
        $handler();
    }
}