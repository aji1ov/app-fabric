<?php

namespace App\Fabric\Route\Middleware;

use App\Fabric\Error\ApiException;
use App\Fabric\Route\Process\Request;
use App\Fabric\Route\Process\Response;

class MiddlewareRunner
{
    private $middlewares;
    private $startFrom = 0;
    private $request;
    private $api_handler;
    private $response;

    public function __construct(Request $request, callable $api_handler, MiddlewareInterface ...$middlewares)
    {
        $this->request = $request;
        $this->middlewares = $middlewares;
        $this->api_handler = $api_handler;
    }

    /**
     * @throws ApiException
     */
    public function next(): Response
    {
        if(!$this->middlewares[$this->startFrom]) return $this->makeResponse();

        $next = $this->middlewares[$this->startFrom++];
        $handle = new MiddlewareHandle($this);

        $next->handle($this->request, $handle);
        return $this->next();

    }

    protected function makeResponse(): Response
    {
        if(!$this->response)
        {
            $handler = $this->api_handler;
            $this->response = $handler();
        }
        return $this->response;
    }
}