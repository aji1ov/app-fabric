<?php

namespace App\Fabric\Error;

use Throwable;

class ApiException extends FabricException
{
    private $error_code;
    private int $status_code;
    public function __construct($message = "", $code = "api_error", int $status_code = 500)
    {
        parent::__construct($message, 0, null);
        $this->error_code = $code;
        $this->status_code = $status_code;
    }

    public function getStatusCode(): int
    {
        return $this->status_code;
    }

    public function getErrorCode(): string
    {
        return $this->error_code;
    }
}