<?php

namespace App\Fabric;

use App\Fabric\Error\FabricException;
use App\Fabric\Route\Middleware\MiddlewareInterface;
use App\Fabric\Route\Reject\RejectInterface;
use App\Fabric\Route\Endpoint;
use App\Fabric\Route\Middleware;
use App\Fabric\Route\Module;

class Route
{
    private static ?Module $module = null;
    /**
     * @var array
     */
    private static array $endpoints = [];

    private static function context(): Module
    {
        if(!static::$module) static::$module = new Module('.');
        return static::$module;
    }

    public static function module(string $base_url, callable $handle): void
    {
        $parent = static::context();
        $server = new Module($base_url, $parent);
        static::$module = $server;
        $handle();
        static::$module = $parent;
    }

    /**
     * @param MiddlewareInterface|string ...$middleware
     * @throws FabricException
     */
    public static final function middleware(MiddlewareInterface|string ...$middleware): void
    {
        static::context()->concatMiddlewares(Middleware::normalizeMiddlewares($middleware));
    }

    /**
     * @param MiddlewareInterface ...$middleware
     * @throws FabricException
     */
    public static final function excludeMiddleware(MiddlewareInterface ...$middleware): void
    {
        static::context()->concatExcludedMiddlewares(Middleware::normalizeMiddlewares($middleware));
    }

    public static function rejectBy(RejectInterface $reject)
    {
        static::context()->setRejectInterface($reject);
    }

    public static function create(string $url): Module
    {
        return new Module($url, static::context());
    }

    public static function api(Endpoint $endpoint): void
    {
        static::$endpoints[] = $endpoint;
    }

    public static function get(string $url, string|callable $handler): void
    {
        static::api((new Endpoint(static::create($url)))->method(Endpoint::GET)->call($handler));
    }

    public static function post(string $url, string|callable $handler): void
    {
        static::api((new Endpoint(static::create($url)))->method(Endpoint::POST)->call($handler));
    }

    public static function any(string $url, string|callable $handler): void
    {
        static::api((new Endpoint(static::create($url)))->call($handler));
    }

    public static function getEndpoints(): array
    {
        return static::$endpoints;
    }
}