<?php

namespace App\Fabric\Error;

class ValidateException extends ApiException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 'api_validate_error', 503);
    }
}