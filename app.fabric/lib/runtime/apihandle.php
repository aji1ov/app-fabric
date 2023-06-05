<?php

namespace Api\Fabric\Runtime;

use App\Fabric\Route\Handle;
use App\Fabric\Route\Process\Request;
use App\Fabric\Route\Process\Response;

class ApiHandle extends Handle
{
    private $callback;
    public function __construct(Request $request, callable $callback)
    {
        parent::__construct($request);
        $this->callback = $callback;
    }

    public function prepare(): void {}

    public function process(): Response
    {
        return \Closure::bind($this->callback, $this)->call($this, $this);
    }
}