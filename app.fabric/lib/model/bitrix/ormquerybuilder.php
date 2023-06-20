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
        $this->query = clone $query;
        $this->map = $map;
    }

    protected function updateOrder()
    {
        /** @var Query\SortEntity $sortEntity */
        foreach($this->query->getSortEntities() as $sortEntity)
        {
            $this->orm->addOrder(strtoupper($sortEntity->getKey()), $sortEntity->getDirection()->value);
        }
    }

    protected function updateLimits(): void
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

    protected function updateFilters(): void
    {
        $this->addFilterEntities($this->orm->getFilterHandler(), ...$this->query->getFilterEntities());
    }

    protected function addSubFilter(ConditionTree $tree, SubFilter $subFilter): void
    {
        $sub = OrmQuery::filter();
        $sub->logic($subFilter->getLogic() === Query\FilterLogic::AND ? 'and' : 'or');

        $this->addFilterEntities($sub, ...$subFilter->getFilterEntities());
        $tree->where($sub);
    }

    protected function addFilterEntities(ConditionTree $tree, FilterEntity ...$entities): void
    {
        foreach($entities as $entity)
        {
            $this->addFilterEntity($tree, $entity);
        }
    }


    /**
     * @throws ArgumentException
     * @throws FabricException
     */
    protected function addFilterEntity(ConditionTree $tree, FilterEntity $entity): void
    {
        $key = new FilterEntityKey($this->query, $entity->getKey());
        $value = new FilterEntityValue($entity->getValue());

        if($value->isRelated())
        {
            $tree->whereColumn(
                $key->build($this->relations),
                $entity->getOperator()->value,
                $value->build($this->relations)
            );
        }
        else
        {
            $tree->where(
                $key->build($this->relations),
                $entity->getOperator()->value,
                $value->build($this->relations)
            );
        }
    }

    protected function getJoinName(string $relation_name)
    {
        return 'relation_'.$relation_name;
    }

    protected function updateRelation(MapRelation $relation, string $name, ?Query\Join $runtime = null)
    {
        $orm_join = new ConditionTree();
        $orm_join->whereColumn(
            'this.'.strtoupper($relation->getRelationName()),
            '=',
            'ref.'.strtoupper($relation->getPrimaryField())
        );
        $field = new \Bitrix\Main\Entity\ReferenceField(
            $name,
            $relation->getRelationModelClass()::getOrmReference(),
            $orm_join
        );

        $join_conditions = [];

        if($runtime)
        {

            $field->configureJoinType(match ($runtime->getType()) {
                Query\JoinType::LEFT => Join::TYPE_LEFT,
                Query\JoinType::RIGHT => Join::TYPE_RIGHT,
                Query\JoinType::INNER => Join::TYPE_INNER,
            });

            foreach($runtime->getJoinFilterEntities() as $entity)
            {
                $key = new FilterEntityKey($this->query, $entity->getKey());
                if(!$key->isRelated())
                {
                    $join_conditions[] = $entity;
                }
                else
                {
                    $this->query->add($entity);
                }
            }

            foreach($join_conditions as $condition)
            {
                $key = new FilterEntityKey($this->query, $condition->getKey());
                $value = new FilterEntityValue($condition->getValue());

                if($value->isRelated() && $value->isFromModel($name))
                {
                    $orm_join->whereColumn(
                        $key->buildJoin(),
                        $condition->getOperator()->value,
                        $value->buildJoin()
                    );
                }
                else
                {
                    $orm_join->where(
                        $key->buildJoin(),
                        $condition->getOperator()->value,
                        $value->buildJoin()
                    );
                }
            }
        }

        $this->orm->registerRuntimeField($name, $field);
        $this->orm->addSelect($name.".*", $name."_");

    }

    /**
     * @throws ArgumentException
     * @throws FabricException
     * @throws SystemException
     */
    protected function updateJoin(Query\Join $runtime, string $name, string $filter_model)
    {
        $join_conditions = [];

        foreach($runtime->getJoinFilterEntities() as $filterEntity)
        {
            $key = new FilterEntityKey($this->query, $filterEntity->getKey());
            if(!$key->isRelated())
            {
                $join_conditions[] = $filterEntity;
            }
            else
            {
                $this->query->add($filterEntity);
            }
        }

        if(!$join_conditions)
        {
            throw new FabricException("No affected join condition for `".$filter_model."` model");
        }

        $orm_join = new ConditionTree();
        foreach($join_conditions as $condition)
        {
            $key = new FilterEntityKey($this->query, $condition->getKey());
            $value = new FilterEntityValue($condition->getValue());

            $orm_join->whereColumn(
                $key->buildJoin(),
                $condition->getOperator()->value,
                $value->buildJoin()
            );
        }

        $field = new \Bitrix\Main\Entity\ReferenceField(
            $name,
            $runtime->getReferenceModel()::getOrmReference(),
            $orm_join
        );
        $field->configureJoinType(match ($runtime->getType()) {
            Query\JoinType::LEFT => Join::TYPE_LEFT,
            Query\JoinType::RIGHT => Join::TYPE_RIGHT,
            Query\JoinType::INNER => Join::TYPE_INNER,
        });

        $this->orm->registerRuntimeField($name, $field);
    }

    protected function updateJoins(): void
    {
        $runtimes = $this->query->getJoins();
        //relations
        foreach($this->map->getRelations() as $name => $map_relation)
        {
            $code = $this->getJoinName($name);
            $this->relations[$name] = $code;

            if($runtime = $runtimes[$name])
            {
                $this->updateRelation($map_relation, $code, $runtime);
                unset($runtimes[$name]);
            }
            else
            {
                $this->updateRelation($map_relation, $code);
            }
        }

        foreach($runtimes as $name => $runtime)
        {
            $code = $this->getJoinName($name);
            $this->relations[$name] = $code;

            $this->updateJoin($runtime, $code, $name);
        }
    }

    public function updateQuery(): void
    {
        $this->orm->addSelect('*');
        $this->updateOrder();
        $this->updateLimits();
        $this->updateJoins();
        $this->updateFilters();
    }
}