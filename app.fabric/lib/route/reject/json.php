<?php

namespace App\Fabric\Route\Reject;

use App\Fabric\Error\ApiException;
use App\Fabric\Route\Process\Response;

class Json implements RejectInterface
{
    public function format(ApiException $exception): Response
    {
        return (new Response($exception->getStatusCode()))
            ->json([
                'message' => $exception->getMessage(),
                'error' => $exception->getErrorCode(),
                'status' => $exception->getStatusCode()
            ])
            ->header('Content-Type', 'application/json');
    }
}