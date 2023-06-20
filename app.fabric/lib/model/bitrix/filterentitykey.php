<?php

namespace App\Fabric\Model\Bitrix;

use App\Fabric\Error\FabricException;
use App\Fabric\Model\Query;

class FilterEntityKey
{
    private string $model = '';
    private string $field;
    private string $raw;
    private Query $query;

    /**
     * @throws FabricException
     */
    public function __construct(Query $query, string $key)
    {
        $this->query = $query;
        $this->raw = $key;
        $this->bind($key);
    }

    /**
     * @throws FabricException
     */
    private function bind(string $key): void
    {
        if(str_contains($key, '.'))
        {
            list($model, $field) = explode(".", $key);
            $this->model = $model;
            $this->field = $field;
        }
        else if(str_starts_with($key, '@'))
        {
            if($meaning = $this->query->findAlias(substr($key, 1)))
            {
                $this->bind($meaning);
            }
            else
            {
                throw new FabricException('Unknown alias `'.$key.'`');
            }
        }
        else
        {
            $this->field = $key;
        }
    }

    public function isRelated(): bool
    {
        return !!$this->model;
    }

    public function raw(): string
    {
        return $this->raw;
    }

    /**
     * @throws FabricException
     */
    public function build(array $relations_map): string
    {
        if(!$this->isRelated()) return $this->field;
        if($code = $relations_map[$this->model])
        {
            return $code.".".$this->field;
        }

        throw new FabricException("Unknown relation `".$this->model."` (".$this->raw.")");
    }

    public function buildJoin(): string
    {
        if($this->isRelated()) return 'ref.'.strtoupper($this->field);
        return 'this.'.strtoupper($this->field);
    }

}