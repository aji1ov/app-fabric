<?php

namespace App\Fabric\Logger;

use App\Fabric\Logger;
use App\Fabric\Options;
use Monolog\Handler\StreamHandler;

class Branch extends Logger
{
    private string $branchName;
    public function __construct($serviceName, $branchName)
    {
        $this->branchName = $branchName;
        parent::__construct($serviceName);
    }

    protected function init()
    {
        parent::init();

        $this->pushHandler(new StreamHandler(Options::getLogsPath() .$this->getLoggerSubPath().'/'.$this->name.'/'.$this->branchName.'.log'));
    }
}