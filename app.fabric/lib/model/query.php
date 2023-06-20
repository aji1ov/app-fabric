<?php

namespace App\Fabric\Model;


use App\Fabric\Model\Query\Direction;
use App\Fabric\Model\Query\Filter;
use App\Fabric\Model\Query\Frame;
use App\Fabric\Model\Query\Join;
use App\Fabric\Model\Query\JoinType;
use App\Fabric\Model\Query\SortEntity;

class Query extends Filter
{
    private array $joins = [];
    private array $sort = [];
    private array $filter_aliases = [];
    private Frame $frame;

    public function __construct(?Filter $filter = null)
    {
        $this->frame = new Frame();
        if($filter) $this->filter = $filter->getFilterEntities();
    }

    public static function new(): Query
    {
        return new Query();
    }

    public function sortBy(string $code, Direction $direction): static
    {
        $this->sort[] = new SortEntity($direction, $code);
        return $this;
    }

    public function sortAsc(string $code): static
    {
        return static::sortBy($code, Direction::ASC);
    }

    public function join(string $reference, string $model, JoinType $type = JoinType::LEFT): Join
    {
        $join = new Join($type, $model);
        $this->joins[$reference] = $join;
        return $join;
    }


    public function take(?int $limit): static
    {
        $this->frame->setLimit($limit);
        return $this;
    }

    public function getSortEntities(): array
    {
        return $this->sort;
    }

    /**
     * @return Join[]
     */
    public function getJoins(): array
    {
        return $this->joins;
    }

    public function getFrame(): Frame
    {
        return $this->frame;
    }

    public function extendFilter(Filter $filter): static
    {
        $this->filter = array_merge($this->filter, $filter->getFilterEntities());
        return $this;
    }

    public function extend(Query $query): static
    {
        $this->extendFilter($query);
        $this->joins = array_merge($this->joins, $query->joins);
        $this->filter_aliases = array_merge($this->filter_aliases, $query->filter_aliases);
        return $this;
    }

    public function alias(string $name, string $meaning): static
    {
        $this->filter_aliases[$name] = $meaning;
        return $this;
    }

    public function findAlias(string $alias): ?string
    {
        return $this->filter_aliases[$alias];
    }
}