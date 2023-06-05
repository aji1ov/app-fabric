<?php

namespace App\Fabric\Error;

use Throwable;

class EventException extends FabricException
{
    private $error_code;
    public function __construct($message = "", $code = "event_error", Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->error_code = $code;
    }

    public function getErrorCode()
    {
        return $this->error_code;
    }
}