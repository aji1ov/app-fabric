<?php

namespace App\Fabric\Runtime;

final class Container extends \App\Fabric\Container
{
    private string $name;
    private $bootstrap;
    private function __construct($name, ?callable $bootstrap = null)
    {
        $this->name = $name;
        $this->bootstrap = $bootstrap;
        parent::__construct();
    }

    public final static function create(string $serviceName, ?callable $bootstrap = null): \App\Fabric\Container
    {
        return new Container('runtime.'.$serviceName, $bootstrap);
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected function bootstrap()
    {
        if($this->bootstrap)
        {
            $func = $this->bootstrap;
            $func();
        }
    }
}