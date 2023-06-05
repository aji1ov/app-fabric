<?php

namespace App\Fabric\Route;

use App\Fabric\Route\Process\Request;
use App\Fabric\Route\Process\Response;

interface HandleInterface
{
    public function prepare(): void;
    public function process(): Response;
}