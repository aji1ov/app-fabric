<?php

namespace App\Fabric\Logger;

use App\Fabric\Options;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FilterHandler;
use Monolog\Handler\StreamHandler;

abstract class ILogger extends \Monolog\Logger
{
    protected bool $useServices = true;
    protected function __construct($serviceName, $useServices = true)
    {
        parent::__construct($serviceName);
        $this->useServices = $useServices;
        $this->init();
    }

    protected function getLoggerSubPath(): string
    {
        return date("Y")."/".date("m")."/".date("j");
    }

    protected function init()
    {
        $logPath = Options::getLogsPath();

        $paths = [
            $logPath.'/#PATH#/all.log',
            $logPath.'/#PATH#/#LEVEL#.log',
        ];

        if($this->useServices)
        {
            $paths = array_merge($paths, [
                $logPath.'/#PATH#/#NAME#/all.log',
                $logPath.'/#PATH#/#NAME#/#LEVEL#.log',
            ]);
        }

        $subpath = $this->getLoggerSubPath();

        $formatter = new LineFormatter(LineFormatter::SIMPLE_FORMAT, "j.m.Y H:i:s");
        $formatter->includeStacktraces(true);

        foreach (\Monolog\Logger::$levels as $level => $levelCanonicName)
        {
            foreach($paths as $path)
            {
                $levelName = $level > \Monolog\Logger::WARNING ? 'error' : 'info';

                $stream = new FilterHandler(
                    new StreamHandler(str_replace(
                            ['#LEVEL#', '#NAME#', '#PATH#'],
                            [$levelName, $this->name, $subpath],
                            $path)
                    ),
                    $level,
                    $level
                );

                $stream->setFormatter($formatter);
                $this->pushHandler($stream);
            }
        }
    }
}