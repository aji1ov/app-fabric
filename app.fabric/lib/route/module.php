<?php

namespace App\Fabric\Route;

use App\Fabric\Route\Reject\RejectInterface;

class Module
{
    private string $path;
    private array $middlewares = [];
    private array $excludedMiddlewares = [];
    private ?Module $parent;
    private ?RejectInterface $reject = null;

    public function __construct(string $path, ?Module $parent = null)
    {
        $this->parent = $parent;
        $this->path = $parent ? static::concat($parent->path, $path) : $path;
    }

    public function concatMiddlewares(array $middlewares)
    {
        $this->middlewares = array_unique(array_merge($this->middlewares, $middlewares), SORT_REGULAR);
    }

    public function concatExcludedMiddlewares(array $middlewares)
    {
        $this->excludedMiddlewares = array_unique(array_merge($this->excludedMiddlewares, $middlewares), SORT_REGULAR);
    }

    public function setRejectInterface(RejectInterface $reject): void
    {
        $this->reject = $reject;
    }

    public function getRejectInterface(): ?RejectInterface
    {
        if(!$this->reject && $this->parent) return $this->parent->getRejectInterface();
        return $this->reject;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function getExcludedMiddlewares(): array
    {
        return $this->excludedMiddlewares;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function getParentModule(): ?Module
    {
        return $this->parent;
    }
    
    public function startWith(string $path): bool
    {
        if(!$this->parent) return false;
        if(preg_match("~^".$this->path."~", $path)) return true;
        return false;
    }

    private static function concat(string $head, string $tail): string
    {
        if($head === '.') return $tail;

        $h = $head === '/' ? [] : explode("/", trim($head, "/"));
        $t = explode("/", trim($tail, "/"));

        return "/".implode("/", array_merge($h, $t))."/";
    }
}