<?php

namespace App\Fabric\Model\Bitrix;

use App\Fabric\Error\FabricException;

class FilterEntityValue
{
    private string $model = '';
    private mixed $field;
    private mixed $raw;

    public function __construct(mixed $key)
    {
        $this->raw = $key;
        if(is_string($key) && str_contains($key, '.') && str_starts_with($key, ":"))
        {
            list($model, $field) = explode(".", substr($key, 1));
            $this->model = $model;
            $this->field = $field;
        }
        else
        {
            $this->field = $key;
        }
    }

    public function raw(): mixed
    {
        return $this->raw;
    }

    public function isRelated(): bool
    {
        return !!$this->model;
    }

    public function isFromModel(string $model): bool
    {
        return $this->model === $model;
    }

    /**
     * @throws FabricException
     */
    public function build(array $relations_map): mixed
    {
        if(!$this->isRelated()) return $this->field;
        if($code = $relations_map[$this->model])
        {
            return $code.".".$this->field;
        }

        //throw new FabricException("Unknown relation `".$this->model."`");
        return $this->field;
    }

    public function buildJoin(): string
    {
        if($this->isRelated()) return 'ref.'.strtoupper($this->field);
        return $this->field;
    }

}