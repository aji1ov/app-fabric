<?php

namespace App\Fabric\Model\Bitrix;

use App\Fabric\Error\FabricException;
use App\Fabric\Model\BitrixModel;
use App\Fabric\Model\Map;
use App\Fabric\Model\MapRelation;
use App\Fabric\Model\Query;
use App\Fabric\Model\Query\FilterEntity;
use App\Fabric\Model\Query\SubFilter;
use App\Fabric\Model\Relation;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\ORM\Query\Query as OrmQuery;
use Bitrix\Main\SystemException;

class OrmQueryBuilder
{
    private OrmQuery $orm;
    private Query $query;
    private Map $map;

    private array $relations = [];

    public function __construct(OrmQuery $orm, Query $query, Map $map)
    {
        $this->orm = $orm;
        $this->query = $query;
        $this->map = $map;
    }

    public function createRelationJoin(MapRelation $relation)
    {
        return new \Bitrix\Main\Entity\ReferenceField(
            $relation->getPropertyName(),
            $relation->getRelationModelClass()::getOrmReference(),
            \Bitrix\Main\ORM\Query\Join::on('this.'.$relation->getRelationName(), 'ref.'.$relation->getPrimaryField())
        );
    }

    /**
     * @throws ArgumentException
     * @throws FabricException
     * @throws SystemException
     */
    public function createRuntimeJoin(Query\Join $join, $relation_name): ReferenceField
    {
        $join_conditions = [];
        $filter_conditions = [];

        foreach($join->getJoinFilterEntities() as $filterEntity)
        {
            if($this->canBeJoinCondition($filterEntity, $join, $relation_name))
            {
                $join_conditions[] = $filterEntity;
            }
            else
            {
                $filter_conditions[] = $filterEntity;
            }
        }

        if(!$join_conditions)
        {
            throw new FabricException("No affected join condition for `".$relation_name."` model");
        }

        $orm_join = new ConditionTree();
        foreach($join_conditions as $condition)
        {
            $orm_join->whereColumn(
                $this->makeJoinEntity($condition->getKey(), $relation_name),
                $condition->getOperator()->value,
                $this->makeJoinEntity($condition->getValue(), $relation_name)
            );
        }

        $this->pushFilter($filter_conditions);

        return new \Bitrix\Main\Entity\ReferenceField(
            $relation_name,
            $join->getReferenceModel()::getOrmReference(),
            $orm_join
        );
    }

    /**
     * @param FilterEntity[] $filter
     * @return void
     */
    public function pushFilter(array $filter): void
    {
        $this->pushFilterEntities($this->orm->getFilterHandler(), $filter);
    }

    /**
     * @param SubFilter $subfilter
     * @return void
     * @throws ArgumentException
     */
    public function pushSubFilter(Query\SubFilter $subfilter, ConditionTree $parent): void
    {
        $sub = OrmQuery::filter();
        $sub->logic($subfilter->getLogic() === Query\FilterLogic::AND ? 'and' : 'or');

        $this->pushFilterEntities($sub, $subfilter->getFilterEntities());
        $parent->where($sub);
    }

    /**
     * @throws ArgumentException
     */
    protected function pushFilterEntities(ConditionTree $tree, array $entities)
    {
        foreach($entities as $filterEntity)
        {
            if($filterEntity->getOperator() === Query\Operator::INNER)
            {
                $this->pushSubFilter($filterEntity->getValue(), $tree);
            }
            else
            {
                if($this->relations && $this->canBeJoinCondition($filterEntity, ...$this->relations))
                {
                    $tree->whereColumn($filterEntity->getKey(), $filterEntity->getOperator()->value, $filterEntity->getValue());
                }
                else
                {
                    print_r(['addFilter' => [$filterEntity->getKey(), $filterEntity->getOperator()->value, $filterEntity->getValue()]]);
                    $tree->where($filterEntity->getKey(), $filterEntity->getOperator()->value, $filterEntity->getValue());
                }
            }
        }
    }

    public function pushJoin(string $name, ReferenceField $field, ?MapRelation $relation = null): void
    {
        $this->orm->registerRuntimeField($name, $field);
        if($relation)
        {
            $this->orm->addSelect($name.".*". $name."_");
        }
    }

    protected function makeJoinEntity(string $entity, string $joinModelName): string
    {
        if(str_contains($entity, '.'))
        {
            list($ref, $key) = explode(".", $entity);
            if($ref === $joinModelName)
            {
                return "ref.".strtoupper($key);
            }

            throw new FabricException("unknown join entity ".$ref);
        }
        else
        {
            return "this.".strtoupper($entity);
        }
    }

    public function isProperty(string $key, Query\Join $join, string ...$right): bool
    {
        if(str_contains('.', $key))
        {
            return $this->isRelatedProperty($key, $join, ...$right);
        }

        return !!$this->map->getField($key);
    }

    public function isRelatedProperty(string $key, Query\Join $join, string ...$right): bool
    {
        list($model, $relation_key) = explode(".", $key);
        if(!in_array(strtolower($model), $right)) return false;

        /** @var Map $join_map */
        $join_map = $join->getReferenceModel()::map();
        if(!$join_map->getField($relation_key)) return false;

        return true;
    }

    public function canBeJoinCondition(FilterEntity $entity, Query\Join $join, ?string ...$right): bool
    {
        if(!$right) return false;

        $key = $entity->getKey();
        if(!$this->isProperty($key, $join, ...$right)) return false;

        $value = $entity->getValue();
        if(!$this->isProperty($value, $join, ...$right)) return false;

        return true;
    }

    public function collectJoins(): void
    {
        $query_joins = $this->query->getJoins();
        $model_joins = $this->map->getRelations();

        foreach ($model_joins as $relation_name => $relation)
        {
            $this->relations[] = $relation_name;
            $join = $this->createRelationJoin($relation);
            if($append_join = $query_joins[$relation_name])
            {
                unset($query_joins[$relation_name]);
                if($filter = $append_join->getJoinFilterEntities())
                {
                    $this->pushFilter($filter);
                }
            }
            $this->pushJoin($relation_name, $join, $relation);
        }

        foreach($query_joins as $join_name => $runtime_join)
        {
            $this->relations[] = $join_name;
            $join = $this->createRuntimeJoin($runtime_join, $join_name);
            $this->pushJoin($join_name, $join);
        }
    }

    public function pushOrder(): void
    {
        /** @var Query\SortEntity $sortEntity */
        foreach($this->query->getSortEntities() as $sortEntity)
        {
            $this->orm->addOrder(strtoupper($sortEntity->getKey()), $sortEntity->getDirection()->value);
        }
    }

    public function pushLimits(): void
    {
        if($limit = $this->query->getFrame()->getLimit())
        {
            $this->orm->setLimit($limit);
        }

        if($offset = $this->query->getFrame()->getOffset())
        {
            $this->orm->setOffset($offset);
        }
    }

    public function updateQuery(): void
    {
        $this->orm->addSelect('*');
        $this->collectJoins();
        $this->pushFilter($this->query->getFilterEntities());
        $this->pushOrder();
        $this->pushLimits();

        print_r($this->orm->getQuery());die;
    }
}