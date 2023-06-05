<?php

namespace App\Fabric\Route\Middleware;

use App\Fabric\Route\Process\Response;

class MiddlewareHandle
{
    private MiddlewareRunner $runner;
    public function __construct(MiddlewareRunner $runner)
    {
        $this->runner = $runner;
    }

    public function waitHandlerResponse(): ?Response
    {
        return $this->runner->next();
    }
}