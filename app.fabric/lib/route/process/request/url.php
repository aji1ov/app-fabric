<?php

namespace App\Fabric\Route\Process\Request;

class Url
{
    private string $full;
    private string $relative;
    public function __construct(string $full, string $relative)
    {
        $this->full = $full;
        $this->relative = $relative;
    }

    public function absolutePath(): string
    {
        return $this->full;
    }

    public function relativePath(): string
    {
        return $this->relative;
    }

    public function setRelativePath(string $relative): void
    {
        if(!$this->relative) $this->relative = $relative;
    }
}