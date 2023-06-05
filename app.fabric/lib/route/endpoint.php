<?php

namespace App\Fabric\Route;

use App\Fabric\Route\Middleware\MiddlewareInterface;
use App\Fabric\Route\Util\RouteUrlDefinition;
use App\Fabric\Error\FabricException;

class Endpoint
{
    const GET = 'GET';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const POST = 'POST';


    private Module $module;
    private ?array $method = null;
    private $handle;
    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    public function getModule(): Module
    {
        return $this->module;
    }

    public function method(string ...$method): Endpoint
    {
        $this->method = $method;
        return $this;
    }

    public function call(string|callable $routeHandlerClass): Endpoint
    {
        $this->handle = $routeHandlerClass;
        return $this;
    }

    public function createProcess(string $path): ?Process
    {
        $def = new RouteUrlDefinition($this->module->path(), $path);
        if($def->getResult() === null) return null;

        return new Process($this, $def->getResult());
    }

    public function getMethods(): ?array
    {
        return $this->method;
    }

    public function getHandle(): ?string
    {
        return $this->handle;
    }

    /**
     * @return MiddlewareInterface[]
     */
    public function getAffectedMiddlewares(): array
    {
        $middlewares = [$this->module->getMiddlewares()];
        $excluded = [$this->module->getExcludedMiddlewares()];

        $module = $this->module;
        while($module = $module->getParentModule())
        {
            $middlewares[] = $module->getMiddlewares();
            $excluded[] = $module->getExcludedMiddlewares();
        }

        //print_r([array_merge(...$middlewares), array_merge(...$excluded)]);die;
        $included = array_merge(...$middlewares);
        $excluded = array_merge(...$excluded);

        return array_udiff($included, $excluded, function($left, $right){
            return $left == $right ? 0 : -1;
        });
    }

    /**
     * @param MiddlewareInterface $middlewareClass
     * @throws FabricException
     */
    public function middleware(...$middlewareClass): Endpoint
    {
        $this->module->concatMiddlewares(Middleware::normalizeMiddlewares($middlewareClass));
        return $this;
    }

    /**
     * @param MiddlewareInterface $middlewareClass
     * @throws FabricException
     */
    public function excludeMiddleware(...$middlewareClass): Endpoint
    {
        $this->module->concatExcludedMiddlewares(Middleware::normalizeMiddlewares($middlewareClass));
        return $this;
    }
}