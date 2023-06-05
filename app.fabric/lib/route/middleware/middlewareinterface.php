<?php

namespace App\Fabric\Route\Middleware;

use App\Fabric\Error\ApiException;
use App\Fabric\Route\Process\Request;
use App\Fabric\Route\Process\Response;

interface MiddlewareInterface
{
    /**
     * @throws ApiException
     */
    public function handle(Request $request, MiddlewareHandle $handle): void;
    public function terminate(Request $request, Response $response);
}