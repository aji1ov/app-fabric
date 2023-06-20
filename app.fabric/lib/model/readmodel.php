<?php

namespace App\Fabric\Model;

use App\Fabric\Model\Query\Filter;
use App\Fabric\Model\Query\Mode;

trait ReadModel
{
    public final static function first(Query|callable|null $handler = null): ?static
    {
        $query = static::createQuery($handler);
        $query->getFrame()->setMode(Query\Mode::FIRST);
        return static::read(static::reader($query));
    }

    /**
     * @internal
     * @param callable|Query|null $handler
     * @return void
     */
    public final static function explain(callable|Query|null $handler = null): void
    {
        $query = static::createQuery($handler);
        $query->getFrame()->setMode(Query\Mode::EXPLAIN);
        static::read(static::reader($query));
    }

    public final static function firstOrCreate(Query|callable|null $handler = null): static
    {
        if($model = static::first($handler))
        {
            return $model;
        }

        return new static();
    }

    public final static function count(callable|Query|null $handler = null): int
    {
        $query = static::createQuery($handler);
        $query->getFrame()->setMode(Query\Mode::COUNT);
        return static::reader($query)();
    }

    /**
     * @param Query|callable|null $handler
     * @return static[]
     */
    public final static function lazy(callable|Query|null $handler = null): \Generator
    {
        $query = static::createQuery($handler);
        $query->getFrame()->setMode(Query\Mode::ALL);
        $pointer = static::reader($query);

        while($model = static::read($pointer))
        {
            yield $model;
        }
    }

    /**
     * @param Query|callable|null $handler
     * @return static[]
     */
    public final static function fetch(callable|Query|null $handler = null): array
    {
        $all = [];
        $query = static::createQuery($handler);
        $query->getFrame()->setMode(Query\Mode::ALL);
        $pointer = static::reader($query);

        while($model = static::read($pointer))
        {
            $all[] = $model;
        }

        return $all;
    }

    /**
     * @param Query|callable|null $handler
     * @return static[]
     */
    public final static function chunk(int $size, callable|Query|null $handler = null): \Generator
    {
        $chunk = [];
        $query = static::createQuery($handler);
        $query->getFrame()->setMode(Query\Mode::ALL);
        $pointer = static::reader($query);

        while($model = static::read($pointer))
        {
            $chunk[] = $model;
            if(count($chunk) >= $size)
            {
                yield $chunk;
                $chunk = [];
            }
        }

        if(count($chunk)) yield $chunk;
    }

    protected static function createQuery(Query|callable|null $handler): Query
    {
        if(is_a($handler, Query::class))
            return $handler;

        $query = new Query();
        if(is_callable($handler))
            $handler($query);

        /** @var VTableEntity $vtable */
        if($vtable = static::map()->seekVTable())
        {
            $query->extendFilter($vtable->getCondition());
        }

        return $query;
    }

    protected static function extendsQuery(Query $parent, Query $child)
    {
        $parent->extendFilter($child);
    }

    public static function primary(mixed $value): static
    {
        return static::first(fn (Query $query) => $query->whereEquals(static::map()->getPrimary()->getModelName(), $value));
    }

    abstract protected static function read(callable $pointer): ?static;
}