<?php

namespace App\Fabric\Logger;

use App\Fabric\Logger;

class Security
{
    private Logger $logger;
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function swallow(callable $function)
    {
        try
        {
            $function($this->logger);
        }
        catch(\Throwable $e){}
    }

    public function continue(callable $function)
    {
        try
        {
            $function($this->logger);
        }
        catch(\Throwable $e)
        {
            $this->logger->alert($e);
        }
    }

    /**
     * @throws \Throwable
     */
    public function throw(callable $function)
    {
        try
        {
            $function($this->logger);
        }
        catch(\Throwable $e)
        {
            $this->logger->error($e);
            throw $e;
        }
    }
}