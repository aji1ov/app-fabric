<?php

namespace App\Fabric\Logger;

interface LoggerInterface extends \Psr\Log\LoggerInterface
{
    public function branch(string $branchName): \Psr\Log\LoggerInterface;

    public function secure(string $branchName = null): Security;
}