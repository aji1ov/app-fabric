<?php

namespace App\Fabric\Model\Query;

class SubFilter extends Filter
{
    private FilterLogic $logic;
    public function __construct(FilterLogic $logic = FilterLogic::AND)
    {
        $this->logic = $logic;
    }

    public function getLogic(): FilterLogic
    {
        return $this->logic;
    }
}