<?php

namespace App\Fabric\Model\Iblock;


class Query
{
    private array $filter = [];
    private array $order = [];
    private int $limit = 0;

    public function __construct(){}

    public function mergeFilter(array $filter): static
    {
        $this->filter = array_merge($this->filter, $filter);
        return $this;
    }

    public function overrideFilter(array $filter): static
    {
        $this->filter = $filter;
        return $this;
    }

    public function filter(string $code, $value): static
    {
        $this->filter[$code] = $value;
        return $this;
    }

    public function getFilter(): array
    {
        return $this->filter;
    }


    public function mergeOrder(array $order): static
    {
        $this->order = array_merge($this->order, $order);
        return $this;
    }

    public function overrideOrder(array $order): static
    {
        $this->order = $order;
        return $this;
    }

    public function order(string $code, $value): static
    {
        $this->order[$code] = $value;
        return $this;
    }

    public function getOrder(): array
    {
        return $this->order;
    }

    public function once(): static
    {
        $this->limit = 1;
        return $this;
    }

    public function isOnce(): bool
    {
        return $this->limit == 1;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}