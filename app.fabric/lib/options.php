<?php

namespace App\Fabric;

class Options
{
    public static function getLogsPath(): string
    {
        return $_SERVER["DOCUMENT_ROOT"]."/upload/fabric/logs/";
    }

    public static function getSecret(): string
    {
        return 'TEST_SECRET_KEY';
    }
}