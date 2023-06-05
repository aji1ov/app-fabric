<?php

namespace App\Fabric\Route\Util;

class RouteUrlDefinition
{
    private string $pattern;
    private ?array $result;
    public function __construct(string $template, string $path)
    {
        $this->pattern = static::compileUrlTemplatePattern($template);
        $this->result = $this->test($path);
    }

    private static function compileUrlTemplatePattern(string $mask): string
    {
        return "~^".preg_replace("(\{(.+?)\})", "(?P<$1>[^\/]+)", $mask)."$~";
    }

    private function test(string $path): ?array
    {
        if(preg_match($this->pattern, $path, $match))
        {
            return array_filter($match, function($k){return !is_numeric($k);}, ARRAY_FILTER_USE_KEY);
        }

        return null;
    }

    public function getResult(): ?array
    {
        return $this->result;
    }
}