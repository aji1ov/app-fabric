<?php

namespace App\Fabric\Model;

use App\Fabric\Error\FabricException;
use App\Fabric\Model;

class Map
{
    /**
     * @var $collection Map[]
     */
    private static array $collection = [];

    /**
     * @var VTableEntity[]
     */
    private static array $vtables = [];

    /**
     * @var VTableEntity[]
     */
    private static array $vtables_seek = [];

    private string $class;

    /**
     * @var $fields MapProperty[]
     */
    private array $fields = [];

    /**
     * @var $relations MapRelation[]
     */
    private array $relations = [];

    /**
     * @var $references MapReference[]
     */
    private array $references = [];

    private ?MapProperty $primary;

    /**
     * @var Model[]
     */
    private static array $entities_cache = [];

    public function __construct(string $modelClass)
    {
        if(!is_a($modelClass, Model::class, true))
        {
            throw new FabricException('$modelClass must be pointer to Model class');
        }

        $this->class = $modelClass;
        $this->build();
    }

    private function build(): void
    {
        $reflect = new \ReflectionClass($this->class);
        foreach($reflect->getProperties() as $property)
        {
            foreach($property->getAttributes() as $attribute)
            {
                $propertyName = $property->getName();

                if(is_a($attribute->getName(), Field::class, true))
                {
                    /** @var Field $instance */
                    $instance = $attribute->newInstance();

                    $map_property = new MapProperty($property, $instance);
                    $this->fields[$propertyName] = $map_property;

                    if(is_a($attribute->getName(), Primary::class, true))
                    {
                        $this->primary = $map_property;
                    }
                }
                else if(is_a($attribute->getName(), Relation::class, true))
                {
                    /** @var Relation $instance */
                    $instance = $attribute->newInstance();
                    $this->relations[$propertyName] = new MapRelation($property, $instance);
                }
                else if(is_a($attribute->getName(), Reference::class, true))
                {
                    if(!is_a($property->getType()->getName(), Lazy::class, true))
                    {
                        throw new FabricException("Property ".$propertyName." must has Lazy type");
                    }
                    $this->references[$propertyName] = new MapReference($property->getType()->getName(), $property);
                }
            }
        }
    }

    public function getPrimary(): ?MapProperty
    {
        return $this->primary;
    }

    /**
     * @return MapProperty[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getRelation(string $modelClass): MapRelation
    {
        return $this->relations[$modelClass];
    }

    /**
     * @return MapRelation[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * @param string $name
     * @return MapReference|null
     */
    public function getReference(string $name): ?MapReference
    {
        return $this->references[$name];
    }

    /**
     * @return MapReference[]
     */
    public function getReferences(): array
    {
        return $this->references;
    }

    /**
     * @return MapProperty
     */
    public function getField(string $key): MapProperty
    {
        return $this->fields[$key];
    }

    private static function getEntityKey(Model $model): ?string
    {
        $primary = $model::map()->getPrimary()->getValue($model);
        if(!is_set($primary)) return null;

        $cmodel = get_class($model);
        if($parent = static::$vtables_seek[$cmodel]) $cmodel = $parent->getParentModel();

        return $cmodel.$primary;
    }

    public static function fromCache(string $modelClass, mixed $primary): ?Model
    {
        if($model = static::$entities_cache[$modelClass.$primary])
        {
            return $model;
        }
        return null;
    }

    public function restore(ValueSource $source, bool $escape_nulls = false): Model
    {
        $model = null;
        if($vtables = static::tryVTables($this->class))
        {
            foreach($vtables as $vtable)
            {
                if($childModel = $vtable->tryCreate($source))
                {
                    $model = $childModel;
                    break;
                }
            }
        }

        if(!$model) $model = new $this->class;

        if($this->primary->hasValue($source))
        {
            foreach($this->fields as $property)
            {
                $property->insertValue($model, $source, $escape_nulls);
            }
        }

        foreach($this->relations as $relation)
        {
            $fork = $source->fork($relation->getCode()."_");
            $relationClass = $relation->getRelationModelClass();

            if($fork->isEmpty())
            {
                if($relation_model = static::fromCache(
                    $relationClass,
                    $model::map()->getField($relation->getRelationName())->getValue($model)
                ))
                {
                    $relation->insert($model, $relation_model);
                }
                else
                {
                    $relation->insert(
                        $model,
                        $relationClass::primary($model::map()->getField($relation->getRelationName())->getValue($model))
                    );
                }
            }
            else
            {
                if(!$relationClass::map()->getPrimary()->getReferenceValue($fork)) continue;
                $relation->create($model, $fork);
            }
        }

        foreach($this->references as $name => $reference)
        {
            $reference->create($model, $name);
        }

        if($key = static::getEntityKey($model))
        {
            /*if(static::$entities_cache[$key])
            {
                print_r(['override' => $model]);
            }*/
            static::$entities_cache[$key] = $model;
        }


        return $model;
    }

    public function dump(Model $model): ValueSource
    {
        $source = new ValueSource([]);
        foreach($this->fields as $property)
        {
            $property->extractValue($model, $source);
        }

        return $source;
    }

    public static function get(string $modelClass): Map
    {
        if(!static::$collection[$modelClass])
        {
            static::$collection[$modelClass] = new Map($modelClass);
        }

        return static::$collection[$modelClass];
    }

    /**
     * @param string $parentModel
     * @return VTableEntity[]
     */
    public static function tryVTables(string $parentModel): ?array
    {
        return static::$vtables[$parentModel];
    }

    /**
     * @param VTable $class
     * @return void
     */
    public static function vtable(string $class): void
    {
        $filter = new Query\Filter();
        $parent = $class::link($filter);

        $entity = new VTableEntity($parent, $class, $filter);

        static::$vtables[$parent][] = $entity;
        static::$vtables_seek[$class] = $entity;
    }

    public function seekVTable(): ?VTableEntity
    {
        return static::$vtables_seek[$this->class];
    }
}