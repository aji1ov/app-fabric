<?php

namespace App\Fabric\Route\Process;

use App\Fabric\Route\Endpoint;

class RouteResult
{
    private bool $result;
    private ?array $definitions;
    private Endpoint $endpoint;
    public function __construct(Endpoint $endpoint, bool $result = false, ?array $definitions = [])
    {
        $this->endpoint = $endpoint;
        $this->result = $result;
        $this->definitions = $definitions;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->result === true;
    }

    /**
     * @return array|null
     */
    public function getDefinitions(): ?array
    {
        return $this->definitions;
    }

    /**
     * @return Endpoint
     */
    public function getEndpoint(): Endpoint
    {
        return $this->endpoint;
    }
}