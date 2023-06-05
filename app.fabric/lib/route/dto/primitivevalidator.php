<?php

namespace App\Fabric\Route\Dto;


use App\Fabric\Error\DtoValidateException;

class PrimitiveValidator
{
    const STRING = 'string';
    const MIXED = 'mixed';
    const INTEGER = 'int';
    const FLOAT = 'float';
    const ARRAY = 'array';
    const BOOLEAN = 'bool';

    const NULL = 'null';
    const EMPTY = 'empty';

    private static $compatibility = [
        PrimitiveValidator::FLOAT => [PrimitiveValidator::INTEGER],
        PrimitiveValidator::STRING => [PrimitiveValidator::INTEGER, PrimitiveValidator::FLOAT, PrimitiveValidator::BOOLEAN],
    ];


    public function __construct(){}

    /**
     * @throws DtoValidateException
     */
    public static function validity(Property $rule, $value, $keyDetected = false)
    {
        $detected = static::detectType($value, $keyDetected);

        $type = $rule->getType();

        if($type === static::MIXED) return $value;

        if($compatibility = static::$compatibility[$type])
        {
            static::checkTypes($rule, $detected, $type,  ...$compatibility);
        }
        else
        {
            static::checkTypes($rule, $detected, $type);
        }

        /*switch($rule->getType())
        {
            case static::INTEGER:
                static::checkTypes($rule, $detected, static::INTEGER);
                break;
            case static::FLOAT:
                static::checkTypes($rule, $detected, static::FLOAT, static::INTEGER);
                break;
            case static::BOOLEAN:
                static::checkTypes($rule, $detected, static::BOOLEAN);
                break;
            case static::ARRAY:
                static::checkTypes($rule, $detected, static::ARRAY);
                break;
            case static::STRING:
                static::checkTypes($rule, $detected, static::STRING, static::INTEGER, static::FLOAT, static::BOOLEAN);
                break;
            default:
                static::checkTypes($rule, $detected, static::MIXED, static::STRING, static::INTEGER, static::FLOAT, static::BOOLEAN, static::ARRAY);
        }*/

        return $value;
    }


    /**
     * @throws DtoValidateException
     */
    public static function checkTypes(Property $rule, string $detected, string $waited, string ...$alternative)
    {
        if($rule->isRequired() && ($detected === static::EMPTY || $detected === static::NULL))
            throw new DtoValidateException(DtoValidateException::REQUIRED);

        if($rule->isNotnull() && $detected === static::NULL)
            throw new DtoValidateException(DtoValidateException::NOT_NULL);

        if($detected !== $waited && !in_array($detected, $alternative) && !in_array($detected, [static::NULL, static::EMPTY]))
            throw new DtoValidateException(DtoValidateException::COMPATIBILITY, $waited, $detected);
    }


    public static function detectType($type, $keyDetected = false)
    {
        if(!$keyDetected) return static::EMPTY;
        if(!isset($type) || !strlen($type) || strtolower($type) === 'null') return static::NULL;

        if(is_array($type)) return static::ARRAY;

        if(is_numeric($type))
        {
            if(is_double($type)) return static::FLOAT;
            return static::INTEGER;
        }

        if(strtolower($type) === 'true' || strtolower($type) === 'false' || is_bool($type)) return static::BOOLEAN;

        return static::STRING;
    }
}