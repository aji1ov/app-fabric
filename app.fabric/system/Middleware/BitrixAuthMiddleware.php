<?php

namespace App\Fabric\System\Middleware;

use App\Fabric\Error\ApiException;
use App\Fabric\Route\Middleware;
use App\Fabric\Route\Middleware\MiddlewareHandle;
use App\Fabric\Route\Process\Request;

class BitrixAuthMiddleware extends Middleware
{

    /**
     * @throws ApiException
     */
    public function handle(Request $request, MiddlewareHandle $handle): void
    {
        global $USER;
        if(!$USER->IsAuthorized())
        {
            $this->interrupt("User must be authorized");
        }

    }
}
