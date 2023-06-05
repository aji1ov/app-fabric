<?php

namespace App\Fabric\Install;

use App\Fabric\Install\Spl\Path;

class Spl
{
    public final static function path(): Path
    {
        return new Path();
    }

    public static function scan($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge(
                [],
                ...[$files, static::scan($dir . "/" . basename($pattern), $flags)]
            );
        }
        return $files;
    }

    public static function load(...$masks)
    {
        $files = [];
        foreach($masks as $mask)
        {
            $files = array_merge($files, Spl::scan($mask));
        }

        foreach($files as $file)
        {
            include_once $file;
        }
    }

    public static function filter(string ...$namespaces): array
    {
        $classes = [];
        foreach (get_declared_classes() as $cls)
        {
            foreach($namespaces as $namespace)
            {
                if(strpos($cls, $namespace) === 0)
                {
                    $classes[] = $cls;
                }
            }
        }

        return $classes;
    }

    private static function findClassPath($class): ?string
    {
        $envs = [static::path()->system(), static::path()->custom()];
        foreach($envs as $env)
        {
            if(strpos($class, $env->namespace()) === 0)
            {
                $try = str_replace($env->namespace(), '', $class);
                return $env->folder().implode("/", explode("\\", $try)).'.php';
            }
        }

        return null;
    }

    public final static function autoload()
    {
        spl_autoload_register(function($class){
            if($path = static::findClassPath($class))
            {
                if(file_exists($path))
                {
                    include_once $path;
                }
            }
        });
    }

    public final static function beforeInstall()
    {
        spl_autoload_register(function($class){
            if(strpos($class, 'App\Fabric\\') === 0)
            {
                $try = str_replace('App\Fabric\\', '', $class);
                include __DIR__.'/../'.implode("/", explode("\\", strtolower($try))).".php";
            }
        });
    }
}