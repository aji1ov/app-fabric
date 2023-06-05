<?php

namespace App\Fabric;

use App\Fabric\Error\ApiException;
use App\Fabric\Event\EventLoader;
use App\Fabric\Install\Spl;
use App\Fabric\Misc\ErrorLogger;
use App\Fabric\Route\Finder;
use App\Fabric\Route\Http;
use App\Fabric\Route\Module;
use App\Fabric\Route\Process\Response;
use App\Fabric\Route\Reject\Html;
use App\Fabric\Schedule\Executor;
use App\Fabric\Schedule\Task;
use Bitrix\Main\Context;
use Symfony\Component\Console\Output\TrimmedBufferOutput;

class Kernel
{
    private static bool $started = false;
    public static function start()
    {
        if(!static::$started)
        {
            Spl::autoload();
            ErrorLogger::getInstance();
            EventLoader::getInstance();

            static::$started = true;

            static::load_route();
            static::run_api();
        }
    }

    private static function format_api_error(Module $module, ApiException $exception): Response
    {
        if($reject = $module->getRejectInterface())
        {
            return $reject->format($exception);
        }
        else
        {
            return (new Html())->format($exception);
        }
    }

    public static function run_api(): void
    {
        $http_request = Context::getCurrent()->getRequest();
        if(file_exists($_SERVER['DOCUMENT_ROOT'].$http_request->getRequestedPage())) return;

        $request = Http::createRequestViaHttpRequest($http_request);

        if($module = Finder::getApiModule($request))
        {
            $response = null;
            try
            {
                if ($process = Finder::tryProcess($request))
                {
                    $response = $process->getResponse($request);
                }
            }
            catch (ApiException $e)
            {
                $response = static::format_api_error($module, $e);
            }

            if(!$response)
            {
                $response = static::format_api_error($module, new ApiException("Route not found", 'api_not_found', 404));
            }

            $http_response = Http::createHttpResponse($response);
            $http_response->flush($http_response->getContent());
            die;
        }
    }

    /**
     * @throws Error\CommandException
     */
    public static function command_argv_run()
    {
        global $argv;

        $arguments = $argv;
        array_shift($arguments);

        $command = $arguments[0];

        \App\Fabric\Command::call(
            $command ?: 'help',
            new \Symfony\Component\Console\Input\ArgvInput($arguments),
            new \Symfony\Component\Console\Output\ConsoleOutput()
        );
    }

    /**
     * @throws Error\CommandException
     */
    public static function command_run_string(string $input): TrimmedBufferOutput
    {
        $output = new TrimmedBufferOutput(1024*1024);
        $arguments = explode(" ", $input);
        $command = $arguments[0];

        \App\Fabric\Command::call(
            $command ?: 'help',
            new \Symfony\Component\Console\Input\ArgvInput($arguments),
            $output
        );

        return $output;
    }

    public static function run_scheduler(): void
    {
        $executor = new Executor();
        $executor->nowait(function(Task $task){
            $job = $task->createJob();
            $job->dump();
        });
    }

    public static function load_route(): void
    {
        include Spl::path()->custom()->folder().'route.php';
    }
}