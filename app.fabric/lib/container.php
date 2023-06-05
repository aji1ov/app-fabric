<?php

namespace App\Fabric;

use App\Fabric\Logger\LoggerInterface;

abstract class Container
{
    private Logger $logger;
    /**
     * @var Logger[]
     */
    private static array $loggers = [];

    public function __construct()
    {
        $this->logger = $this->createLogger();
        $this->bootstrap();
    }

    protected function createLogger(): Logger
    {
        if(!static::$loggers[$this->getName()])
        {
            static::$loggers[$this->getName()] = new Logger($this->getName());
        }
        return static::$loggers[$this->getName()];
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    protected function bootstrap(){}
    abstract public function getName(): string;
}