<?php

namespace App\Fabric\Model\Query;

class Join
{
    private JoinType $type;
    private string $model;
    private array $on = [];

    public function __construct(JoinType $type, string $model)
    {
        $this->type = $type;
        $this->model = $model;
    }

    public function on(string $key, mixed $value): static
    {
        $this->on[] = new FilterEntity(Operator::STRICT_EQUALS, $key, $value);
        return $this;
    }

    /**
     * @return FilterEntity[]
     */
    public function getJoinFilterEntities(): array
    {
        return $this->on;
    }

    public function getType(): JoinType
    {
        return $this->type;
    }

    public function getReferenceModel(): string
    {
        return $this->model;
    }
}
