<?php

namespace App\Fabric\Model\Bitrix;

use App\Fabric\Error\FabricException;
use App\Fabric\Model\Query\Operation;
use Bitrix\Main\Error;

trait BitrixOldWriter
{
    protected static function getWriterClosure(Operation $operation, mixed $primary): \Closure
    {
        $result = new \Bitrix\Main\ORM\Data\UpdateResult();
        $result->setPrimary($primary);

        $tableClass = static::getBOClass();
        $tableObject = new $tableClass();

        if($operation === Operation::DELETE)
        {
            return function() use ($result, $primary, $tableObject)
            {
                if(!$tableObject->Delete($primary))
                {
                    $result->addError(new Error($tableObject->LAST_ERROR));
                }
                return $result;
            };
        }

        if(isset($primary))
        {
            return function ($data) use ($result, $primary, $tableObject)
            {
                if(!$tableObject->Update($primary, $data))
                {
                    $result->addError(new Error($tableObject->LAST_ERROR));
                }

                return $result;
            };
        }
        else
        {
            return function($data) use ($result, $tableObject)
            {
                if($primary = $tableObject->Add($data))
                {
                    $result->setPrimary($primary);
                }
                else
                {
                    $result->addError(new Error($tableObject->LAST_ERROR));
                }

                return $result;
            };
        }
    }

    abstract public static function getBOClass(): string;
}