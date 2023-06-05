<?php

namespace App\Fabric\Route\Process;

use App\Fabric\Route\Process\Request\Body;
use App\Fabric\Route\Process\Request\Query;
use App\Fabric\Route\Process\Request\Url;

class Request
{
    private Url $url;
    private Query $query;
    private Body $body;
    private string $method;
    private ?array $definitions = [];

    public function __construct(Url $url, Query $query, Body $body, string $method)
    {
        $this->url = $url;
        $this->query = $query;
        $this->body = $body;
        $this->method = $method;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function url(): Url
    {
        return $this->url;
    }

    public function query(): Query
    {
        return $this->query;
    }

    public function body(): Body
    {
        return $this->body;
    }

    public function definitions(): ?array
    {
        return $this->definitions;
    }

    public function definition(string $key)
    {
        return $this->definitions[$key];
    }

    public function setDefinitions(?array $definitions): void
    {
        $this->definitions = $definitions;
    }
}
