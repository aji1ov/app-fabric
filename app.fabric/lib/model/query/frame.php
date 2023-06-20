<?php

namespace App\Fabric\Model\Query;

class Frame
{
    private Mode $mode;
    private ?int $limit = null;
    private int $offset = 0;
    private ?int $chunk = null;

    public function __construct()
    {
        $this->mode = Mode::ALL;
    }

    /**
     * @return Mode
     */
    public function getMode(): Mode
    {
        return $this->mode;
    }

    /**
     * @param Mode $mode
     */
    public function setMode(Mode $mode): void
    {
        $this->mode = $mode;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     */
    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * @return int|null
     */
    public function getChunk(): ?int
    {
        return $this->chunk;
    }

    /**
     * @param int|null $chunk
     */
    public function setChunk(?int $chunk): void
    {
        $this->chunk = $chunk;
    }
}