<?php

namespace App\Fabric\Model\Bitrix;

use App\Fabric\Model;
use App\Fabric\Model\Bitrix\Iblock\IBlockElementModel;
use App\Fabric\Model\Bitrix\Iblock\IBlockPropertyValueModel;
use App\Fabric\Model\Query\JoinType;
use ReturnTypeWillChange;


class PropertyCollection extends Model\Collection
{
    private int $pos = 0;
    private ?array $filter = null;
    public function __construct(Model $model, string $name)
    {
        parent::__construct($model, $name);
    }

    public function filter(string ...$codes): static
    {
        print_r(['filter']);
        $this->filter = $codes;
        return $this;
    }

    protected function initialize(): array
    {
        /** @var IBlockElementModel $model */
        $model = $this->getModel();

        $query = new Model\Query();
        $query->join('property', Model\Bitrix\Iblock\IBlockPropertyModel::class, JoinType::RIGHT)->on('iblock_element_id', $model->id);
        $query->whereEquals('property.iblock_id', $model->iblock_id);

        $result = [];

        //IBlockPropertyValueModel::explain($query);
        foreach(IBlockPropertyValueModel::fetch($query) as $model)
        {
            $result[$model->property->code] = $model;
        }
        return $result;
    }

    #[ReturnTypeWillChange]
    public function offsetExists($offset): bool
    {
        return isset($this->pull()[$offset]);
    }

    #[ReturnTypeWillChange]
    public function offsetGet($offset) : ?IBlockPropertyValueModel
    {
        return $this->pull()[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void{}

    public function offsetUnset(mixed $offset): void{}

    public function current(): ?IBlockPropertyValueModel
    {
        return $this->offsetGet($this->key());
    }

    public function next(): void
    {
        $this->pos++;
    }

    public function key(): string|int
    {
        return array_keys($this->pull())[$this->pos];
    }

    public function valid(): bool
    {
        return $this->offsetExists($this->key());
    }

    public function rewind(): void
    {
        $this->pos = 0;
    }
}