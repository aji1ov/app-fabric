<?php

namespace App\Fabric\Route;

use App\Fabric\Route;
use App\Fabric\Error\ApiException;

class Finder
{
    public static function getApiModule(Route\Process\Request $request): ?Module
    {
        $path = $request->url()->relativePath();
        /** @var Endpoint $endpoint */
        foreach(Route::getEndpoints() as $endpoint)
        {
            $module = $endpoint->getModule();
            while(isset($module))
            {
                if($module->startWith($path)) return $module;
                $module = $module->getParentModule();
            }
        }

        return null;
    }

    public static function tryProcess(Route\Process\Request $request): ?Process
    {
        $last_routed = null;
        $path = $request->url()->relativePath();


        /** @var Endpoint $endpoint */
        foreach(Route::getEndpoints() as $endpoint)
        {
            if($process = $endpoint->createProcess($path))
            {
                $methods = $endpoint->getMethods();
                if($methods && !in_array($request->method(), $methods))
                {
                    $last_routed = $endpoint;
                    continue;
                }
                return $process;
            }
        }

        if($last_routed)
            throw new ApiException("No supported routing for `".$request->method()."` method in \"".$path."\", found: ".implode(",", $last_routed->getMethods()));


        return null;
    }

}