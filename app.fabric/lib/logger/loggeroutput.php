<?php

namespace App\Fabric\Logger;

use Symfony\Component\Console\Output\Output;

class LoggerOutput extends Output
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct();
    }

    protected function doWrite($message, $newline)
    {
        $this->logger->info($message . ($newline ? "\n" : ""));
    }
}