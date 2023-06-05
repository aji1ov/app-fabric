<?php

namespace App\Fabric\Route\Process;

class Response
{
    private int $status;
    private string $body;
    private string $type = 'raw';

    private array $headers = [];

    public function __construct(int $status = 200)
    {
        $this->status = $status;
    }

    public function raw(string $text): Response
    {
        $this->body = $text;
        $this->type = 'raw';
        return $this;
    }

    public function json(array $json): Response
    {
        $this->body = json_encode($json);
        $this->header('Content-Type', 'application/json');
        $this->type = 'json';
        return $this;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function header(string $key, string $value): static
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

}