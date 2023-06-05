<?php

namespace App\Fabric\System\Middleware;

use App\Fabric\Route\Middleware;
use App\Fabric\Route\Middleware\MiddlewareHandle;
use App\Fabric\Route\Process\Request;

class BitrixSessionMiddleware extends Middleware
{
    public function handle(Request $request, MiddlewareHandle $handle): void
    {
        if(!check_bitrix_sessid()) $this->interrupt("Current session id:".bitrix_sessid());
    }
}