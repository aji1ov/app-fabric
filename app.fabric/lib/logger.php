<?php

namespace App\Fabric;

use App\Fabric\Logger\Branch;
use App\Fabric\Logger\ILogger;
use App\Fabric\Logger\LoggerInterface;
use App\Fabric\Logger\Security;

class Logger extends ILogger implements LoggerInterface
{
    public function __construct($serviceName)
    {
        parent::__construct($serviceName);
    }

    protected function init()
    {
        parent::init();
    }

    public function branch(string $branchName): \Psr\Log\LoggerInterface
    {
        return new Branch($this->name, $branchName);
    }

    public function secure(string $branchName = null): Security
    {
        $logger = $branchName ? $this->branch($branchName) : $this;
        return new Security($logger);
    }
}