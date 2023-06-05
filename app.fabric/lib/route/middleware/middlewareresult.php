<?php

namespace App\Fabric\Route\Middleware;

use App\Fabric\Error\ApiException;

final class MiddlewareResult
{
    private bool $state;
    private ?ApiException $exception;
    private function __construct(bool $state, ?ApiException $e = null)
    {
        $this->state = $state;
        $this->exception = $e;
    }

    public function isSuccess(): bool
    {
        return $this->state;
    }

    public function getException(): ?ApiException
    {
        return $this->exception;
    }

    public static function success(): MiddlewareResult
    {
        return new MiddlewareResult(true);
    }

    public static function interrupt(?string $message): MiddlewareResult
    {
        return MiddlewareResult::error(new ApiException($message ?:"Middleware interrupted", 503));
    }

    public static function error(ApiException $e): MiddlewareResult
    {
        return new MiddlewareResult(false, $e);
    }
}