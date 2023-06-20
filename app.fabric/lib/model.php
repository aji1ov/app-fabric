<?php

namespace App\Fabric;

use App\Fabric\Error\FabricException;
use App\Fabric\Model\Cache;
use App\Fabric\Model\Map;
use App\Fabric\Model\Query;
use App\Fabric\Model\ReadModel;
use App\Fabric\Model\ValueSource;

abstract class Model
{
    use ReadModel;

    public static function map(): Map
    {
        return Map::get(static::class);
    }

    protected final static function read(callable $pointer): ?static
    {
        if($data = $pointer())
        {
            return static::map()->restore(new Model\ValueSource($data));
        }

        return null;
    }

    public final static function create(array $state): ?static
    {
        if($model = static::map()->restore(new ValueSource($state), true))
        {
            $model->fill();
            return $model;
        }

        return null;
    }

    /**
     * @throws FabricException
     */
    public final function saveOrThrow(): void
    {
        $primary = static::write(new Query\Update(Query\Operation::SAVE, static::map()->dump($this), $this));
        $this::map()->getPrimary()->setValue($this, $primary);
    }

    public final function save(): void
    {
        try
        {
            $this->saveOrThrow();
        } catch(FabricException $e){}
    }

    /**
     * @throws FabricException
     */
    public final function deleteOrThrow(): void
    {
        static::write(new Query\Update(Query\Operation::DELETE, static::map()->dump($this), $this));
    }

    public final function delete(): void
    {
        try
        {
            $this->deleteOrThrow();
        } catch(FabricException $e){}
    }

    public function cache(): Cache
    {
        return Cache::never();
    }

    abstract protected static function reader(Query $query): \Closure;

    /**
     * @throws FabricException
     * @param Query\Update $update
     * @return int
     */
    abstract protected static function write(Query\Update $update): int;

    protected function fill(): void
    {

    }
}