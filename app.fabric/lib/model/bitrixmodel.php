<?php

namespace App\Fabric\Model;

use App\Fabric\Error\FabricException;
use App\Fabric\Model;
use Bitrix\Main\Error;
use Bitrix\Main\Type\DateTime;

abstract class BitrixModel extends Model
{

    use Model\Bitrix\ORM;
    use Model\Bitrix\IBlockDriver;

    protected static function reader(Query $query): \Closure
    {
        $tableClass = static::getOrmReference();
        if(!is_a($tableClass, \Bitrix\Main\ORM\Data\DataManager::class, true))
        {
            throw new FabricException("Class ".$tableClass." is not a orm table");
        }

        print_r(['query_mode' => $query->getFrame()->getMode()]);

        $orm = $tableClass::query();
        $oqb = new Model\Bitrix\OrmQueryBuilder($orm, $query, static::map());
        $oqb->updateQuery();

        if($query->getFrame()->getMode() === Model\Query\Mode::EXPLAIN)
        {
            print_r(['sql' => $orm->getQuery()]);
            return function() use ($orm) {
                return null;
            };
        }

        if($query->getFrame()->getMode() === Model\Query\Mode::COUNT)
        {
            $count = $orm->exec()->getCount();
            return function() use ($count)
            {
                return $count;
            };
        }

        $driver = $orm->exec();
        return function() use ($driver)
        {
            return $driver->fetch();
        };
    }

    protected static function getWriterClosure(Model\Query\Operation $operation, mixed $primary): \Closure
    {
        $tableClass = static::getOrmReference();
        if(!is_a($tableClass, \Bitrix\Main\ORM\Data\DataManager::class, true))
        {
            throw new FabricException("Class ".$tableClass." is not a orm table");
        }

        if($operation === Model\Query\Operation::DELETE)
        {
            return fn() => $tableClass::delete($primary);
        }

        if(isset($primary))
        {
            return fn($data) => $tableClass::update($primary, $data);
        }
        else
        {
            return fn($data) => $tableClass::add($data);
        }
    }

    /**
     * @throws FabricException
     */
    protected static function write(Query\Update $update): int
    {
        $primary = static::map()->getPrimary()->getValue($update->getModel());
        $writer = static::getWriterClosure($update->getOperation(), $primary);

        /** @var $result \Bitrix\Main\ORM\Data\UpdateResult */
        $result = $writer($update->getSource()->getArray());
        if(!$result->isSuccess())
        {
            throw new FabricException($result->getErrorCollection()->current()->getMessage());
        }

        return $primary;
    }
}