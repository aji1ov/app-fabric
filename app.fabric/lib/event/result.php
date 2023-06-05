<?php

namespace App\Fabric\Event;

class Result
{
    private ?string $error_code;

    /** @var string|null $aborted_reason */
    private ?string $aborted_reason = null;

    /** @var mixed|null $result_data */
    private $result_data = null;

    public function abort(string $reason, ?string $error_code = null)
    {
        $this->aborted_reason = $reason;
        $this->error_code = $error_code;
    }

    public function setResultData($data)
    {
        $this->result_data = $data;
    }

    public function getResultData()
    {
        return $this->result_data;
    }

    public function isAborted(): bool
    {
        return !!$this->aborted_reason;
    }

    public function getReason(): ?string
    {
        return $this->aborted_reason;
    }

    public function getErrorCode(): ?string
    {
        return $this->error_code;
    }

    public static function error(string $reason, ?string $error_code = 'event_error'): Result
    {
        $r = new Result();
        $r->abort($reason, $error_code);
        return $r;
    }

    public static function success($resultData): Result
    {
        $r = new Result();
        $r->setResultData($resultData);
        return $r;
    }
}