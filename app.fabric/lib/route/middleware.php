<?php

namespace App\Fabric\Route;

use App\Fabric\Error\ApiException;
use App\Fabric\Error\FabricException;
use App\Fabric\Route\Middleware\MiddlewareInterface;
use App\Fabric\Route\Process\Request;
use App\Fabric\Route\Process\Response;

abstract class Middleware implements MiddlewareInterface
{
    private static array $middlewares;
    public static function set(string $key, MiddlewareInterface ...$middlewares)
    {
        static::$middlewares[$key] = $middlewares;
    }

    /**
     * @param string $key
     * @return Middleware[]
     */
    public static function get(string $key): array
    {
        return static::$middlewares[$key];
    }

    /**
     * @throws FabricException
     */
    public static function check($middleware)
    {
        if(!is_a($middleware, MiddlewareInterface::class, true))
        {
            throw new FabricException("Class(".$middleware.") is not a Middleware");
        }
    }

    /**
     * @throws FabricException
     */
    public static function normalizeMiddlewares(array $middlewares)
    {
        $result = [];
        foreach($middlewares as $middleware)
        {
            $mwares = [];
            if($middleware instanceof MiddlewareInterface)
            {
                $mwares[] = $middleware;
            }
            else if(is_string($middleware))
            {
                $mwares = Middleware::get($middleware);
            }
            else if(is_array($middleware))
            {
                $mwares = $middleware;
            }

            foreach($mwares as $mware)
            {
                static::check($mware);
                $result[] = $mware;
            }
        }

        return array_unique($result, SORT_REGULAR);
    }

    /**
     * @throws ApiException
     */
    public function interrupt(?string $message)
    {
        throw new ApiException($message, 500);
    }

    public function terminate(Request $request, Response $response){}
}