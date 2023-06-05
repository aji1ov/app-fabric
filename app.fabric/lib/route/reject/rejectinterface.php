<?php

namespace App\Fabric\Route\Reject;

use App\Fabric\Error\ApiException;
use App\Fabric\Route\Process\Response;

interface RejectInterface
{
    public function format(ApiException $exception): Response;
}