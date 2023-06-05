<?php

namespace App\Fabric\Event;

use App\Fabric\Error\EventException;
use Bitrix\Main\Event;

class BitrixEventProvider extends EventProvider
{
    private $callback;
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    private function obtainArrayEntity(callable $callback, array &$arFields)
    {
        $emit = new Emit($arFields);
        $result = $this->getResult($emit, $callback);

        if(!$result->isAborted())
        {
            $arFields = $emit->getParameters();
            return $result->getResultData();
        }
        else
        {
            global $APPLICATION;
            $APPLICATION->ThrowException("[".$result->getErrorCode()."] ".$result->getReason());
            return false;
        }
    }

    private function obtainEventEntity(callable $callback, Event $event): \Bitrix\Main\EventResult
    {
        $emit = new Emit($event->getParameters());
        $result = $this->getResult($emit, $callback);
        if(!$result->isAborted())
        {
            $event->setParameters($emit->getParameters());
            return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, $result->getResultData());
        }
        else
        {
            return new \Bitrix\Main\EventResult(
                \Bitrix\Main\EventResult::ERROR,
                \Bitrix\Sale\ResultError::create(
                    new \Bitrix\Main\Error($result->getReason(), $result->getErrorCode())
                )
            );
        }
    }

    private function getResult(Emit $emit, callable $callback): Result
    {
        try
        {
            $answer = $callback($emit);
            if(!empty($answer))
            {
                if($answer instanceof Result)
                {
                    return $answer;
                }
                else
                {
                    return Result::success($answer);
                }
            }
            else
            {
                $result = $emit->getResult();
            }
        }
        catch(EventException $e)
        {
            return Result::error($e->getMessage(), $e->getErrorCode());
        }

        if(!$result) $result = Result::success(null);
        return $result;
    }

    public function __invoke(&$entity)
    {
        if(is_array($entity))
        {
            return $this->obtainArrayEntity($this->callback, $entity);
        }
        else if($entity instanceof Event)
        {
            return $this->obtainEventEntity($this->callback, $entity);
        }

        return null;
    }
}