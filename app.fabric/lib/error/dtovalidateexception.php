<?php

namespace App\Fabric\Error;

class DtoValidateException extends FabricException
{
    const REQUIRED = 'REQUIRED';
    const NOT_NULL = 'NOT_NULL';
    const COMPATIBILITY = 'COMPATIBILITY';

    public string $error_type;
    public ?string $waited;
    public ?string $given;
    public ?string $append;

    public function __construct(string $error_type, string $waited = null, string $given = null, ?string $append = null)
    {
        $this->error_type = $error_type;
        $this->waited = $waited;
        $this->given = $given;
        $this->append = $append;

        parent::__construct("DtoValidityException");
    }
}