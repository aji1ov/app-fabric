<?php

namespace App\Fabric\Misc;

use Monolog\ErrorHandler;
use Psr\Log\LogLevel;

class ErrorLogger extends \App\Fabric\Logger\ILogger
{
    private function __construct()
    {
        parent::__construct('system', false);
    }

    protected function init()
    {
        parent::init();

        $handler = new ErrorHandler($this);
        $handler->registerErrorHandler([], false);
        $handler->registerExceptionHandler([
            \Exception::class => LogLevel::CRITICAL
        ]);
        $handler->registerFatalHandler(LogLevel::EMERGENCY);
    }

    private static ?ErrorLogger $instance = null;
    public static function getInstance(): ErrorLogger
    {
        if(!static::$instance)
        {
            static::$instance = new ErrorLogger();
        }

        return static::$instance;
    }
}