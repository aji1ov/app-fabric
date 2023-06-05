<?php

namespace App\Fabric\Route;

use Api\Fabric\Runtime\ApiHandle;
use App\Fabric\Error\ApiException;
use App\Fabric\Route\Middleware\MiddlewareRunner;
use App\Fabric\Route\Process\Request;
use App\Fabric\Route\Process\Response;

class Process
{
    private Endpoint $endpoint;
    private ?array $definitions;
    public function __construct(Endpoint $endpoint, ?array $definitions = [])
    {
        $this->endpoint = $endpoint;
        $this->definitions = $definitions;
    }

    public function getHandle(Request $request): HandleInterface
    {
        $handle = $this->endpoint->getHandle();
        if(is_a($handle, HandleInterface::class, true))
        {
            return new $handle($request);
        }

        return new ApiHandle($request, $handle);
    }

    public function getResponse(Request $request): ?Response
    {
        $request->setDefinitions($this->definitions);
        $handle = $this->getHandle($request);
        $handle->prepare();

        try
        {
            $response = $this->startMiddlewares($request, function() use ($handle) {
                return $handle->process();
            });

            return $response;
        }
        catch(ApiException $exception)
        {
            if($reject = $this->endpoint->getModule()->getRejectInterface())
            {
                return $reject->format($exception);
            }

            throw $exception;
        }
    }

    /**
     * @throws ApiException
     */
    protected function startMiddlewares(Request $request, callable $handle): Response
    {
        $middlewares = $this->endpoint->getAffectedMiddlewares();
        $runner = new MiddlewareRunner($request, $handle, ...$middlewares);
        return $runner->next();
    }
}
