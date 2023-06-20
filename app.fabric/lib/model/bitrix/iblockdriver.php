<?php

namespace App\Fabric\Model\Bitrix;

use App\Fabric\Error\FabricException;
use App\Fabric\Model;
use App\Fabric\Model\Map;
use App\Fabric\Model\Query;
use Bitrix\Main\DB\SqlExpression;

trait IBlockDriver
{
    private static function makeFilterKey(Query\FilterEntity $entity, Map $map): string
    {
        $filter_key = $entity->getKey();
        if(str_contains($filter_key, '.'))
        {
            list($ref, $key) = explode(".", $filter_key);
            if($reference = $map->getRelation($ref))
            {
                $filter_key = implode(".", [$reference->getCode(), $key]);
            }
            else
            {
                throw new FabricException('Unknown reference `'.$ref.'`');
            }

        }
        return $entity->getOperator()->value.strtoupper($filter_key);
    }

    protected static function createSubFilter(Query\SubFilter $subfilter, Map $map)
    {
        $filter['LOGIC'] = $subfilter->getLogic() === Query\FilterLogic::AND ? 'AND' : 'OR';
        foreach ($subfilter->getFilterEntities() as $filterEntity)
        {
            $filter[] = [static::makeFilterKey($filterEntity, $map) => $filterEntity->getValue()];
        }
        return $filter;
    }

    protected static function createFilterArray(Query $query, Map $map): array
    {
        $filter = [];
        /** @var Query\FilterEntity $filterEntity */
        foreach($query->getFilterEntities() as $filterEntity)
        {
            if($filterEntity->getOperator() === Query\Operator::INNER)
            {
                $filter[] = static::createSubFilter($filterEntity->getValue(), $map);
            }
            else
            {
                $filter[static::makeFilterKey($filterEntity, $map)] = $filterEntity->getValue();
            }
        }

        return $filter;
    }

    private static function createSortArray(Query $query): array
    {
        $sort = [];
        /** @var Query\SortEntity $sortEntity */
        foreach($query->getSortEntities() as $sortEntity)
        {
            $sort[strtoupper($sortEntity->getKey())] = $sortEntity->getDirection()->value;
        }

        return $sort;
    }

    protected static function makeJoinEntity(string $entity, string $joinModelName): string
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

    protected static function makeJoinKey(Query\FilterEntity $entity, string $joinModelName): string
    {
        $key = static::makeJoinEntity($entity->getKey(), $joinModelName);
        return $entity->getOperator()->value.$key;
    }

    protected static function createJoinArray(Query\Join $join, Map $map, string $joinModelName): array
    {
        $filter = [];

        foreach($join->getJoinFilterEntities() as $filterEntity)
        {
            $key = static::makeJoinKey($filterEntity, $joinModelName);
            if(is_string($filterEntity->getValue()))
            {
                $value = static::makeJoinEntity($filterEntity->getValue(), $joinModelName);
            }
            else
            {
                $value = new SqlExpression($filterEntity->getValue());
            }
            $filter[$key] = $value;
        }

        return $filter;
    }

    protected static function createQueryArray(Query $query, Map $map): array
    {
        $getList = [
            'order' => static::createSortArray($query),
            'filter' => static::createFilterArray($query, $map),
            'runtime' => []
        ];

        if($relations = $map->getRelations())
        {
            $joins = $query->getJoins();
            $getList['select'] = [0 => '*'];
            foreach($relations as $property_name => $relation)
            {
                $code = $relation->getCode();
                /** @var Model\BitrixModel $reference */
                $reference = $relation->getRelationModelClass();

                if($relation_value = $query->calculateReferenceValue($relation->getRelationName()))
                {
                    if($model = $map::fromCache($reference, $relation_value))
                    {
                        continue;
                    }
                }

                if($joinEntity = $joins[$property_name])
                {
                    $join = static::join(
                        $relation,
                        $reference::getOrmReference(),
                        $joinEntity->getType()->value,
                        static::createJoinArray($joinEntity, $map, $property_name)
                    );
                }
                else
                {
                    $join = static::join($relation, $reference::getOrmReference());
                }

                $getList['runtime'][$code] = $join;
                $getList['select'][$code."_"] = $code.".*";
            }
        }

        if($limit = $query->getFrame()->getLimit())
        {
            $getList['limit'] = $limit;
        }

        if($offset = $query->getFrame()->getOffset())
        {
            $getList['offset'] = $offset;
        }

        print_r(['getList' => $getList]);

        return $getList;
    }

    protected static function join(Model\MapRelation $relation, string $dataType, string $joinType = 'left', ?array $appendReference = null)
    {
        $reference = ['=this.'.$relation->getRelationName() => 'ref.'.$relation->getPrimaryField()];
        if($appendReference) $reference = array_merge($reference, $appendReference);
        return [
            'data_type' => $dataType,
            'reference' => $reference,
            'join_type' => $joinType
        ];
    }
}