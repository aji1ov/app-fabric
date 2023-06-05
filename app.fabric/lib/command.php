<?php

namespace App\Fabric;

use App\Fabric\Command\Config;
use App\Fabric\Error\CommandException;
use App\Fabric\Install\Spl;
use App\Fabric\System\Container\CommandContainer;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\TrimmedBufferOutput;

abstract class Command
{
    use LockableTrait;

    private \Psr\Log\LoggerInterface $logger;
    public function __construct()
    {
        $this->logger = (new CommandContainer())->getLogger()->branch($this->getCommandName());
    }

    protected function getLogger(): \Psr\Log\LoggerInterface
    {
        return $this->logger;
    }

    protected function isLockable(): bool
    {
        return false;
    }

    public function getCommandName(): string
    {
        $name = preg_replace(
            "/(.*?)Command$/",
            "$1",
            str_replace(
                [Spl::path()->system()->namespace('Command\\'), Spl::path()->custom()->namespace('Command\\')],
                ['',''],
                static::class
            )
        );
        
        return implode(":", array_map(function($e){
            return strtolower($e);
        }, explode("\\", $name)));
    }

    protected static final function tryClass(string $command): ?Command
    {
        $command_ns = implode("\\", array_map(function($e){
            return ucfirst($e);
        }, explode(":", $command)));


        $ns = [
            '\\'.Spl::path()->system()->namespace().'Command\\{CLASS}Command',
            '\\'.Spl::path()->custom()->namespace().'Command\\{CLASS}Command',
        ];

        foreach($ns as $path)
        {
            $command_cls = str_replace("{CLASS}", $command_ns, $path);

            try{
                $command_object = new $command_cls();
                if(is_a($command_object, Command::class, true))
                {
                    return $command_object;
                }
            }catch(\Throwable $e){}
        }

        return null;
    }

    /**
     * @throws CommandException
     */
    public final static function call(string $command, Input $input, Output $output, $blocking = false)
    {
        if($command_object = static::tryClass($command))
        {
            if($command_object->isLockable())
            {
                if(!$command_object->lock($command_object->getCommandName(), $blocking))
                {
                    throw new CommandException("Command is already running in another process");
                }
            }

            try
            {
                $config = new Config();
                $arguments_definition = new InputDefinition();
                $command_object->configure($config);
                $config->fill($arguments_definition);

                $input->bind($arguments_definition);
                $input->validate();

                $command_object->execute($input, $output);
            }
            catch(ExceptionInterface $e)
            {
                throw new CommandException($e->getMessage());
            }


            if($command_object->isLockable())
            {
                $command_object->release();
            }

            return;
        }

        throw new CommandException("Command `".$command."` not found");
    }

    /**
     * @throws CommandException
     */
    public final static function parse(string $command, array $parameters = null): string
    {
        $output = new TrimmedBufferOutput(1024*1024);

        static::call($command, new ArrayInput($parameters ?: []), $output);

        return $output->fetch();
    }

    public final static function listOfCommands(): array
    {
        Spl::load(
            Spl::path()->system()->folder()."Command/*Command.php",
            Spl::path()->custom()->folder()."Command/*Command.php"
        );

        $commandClasses = [];
        foreach (Spl::filter(Spl::path()->system()->namespace(), Spl::path()->custom()->namespace()) as $cls)
        {
            if(is_a($cls, static::class, true) && $cls != static::class)
            {
                $commandClasses[] = $cls;
            }
        }

        return $commandClasses;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws CommandException
     * @return mixed
     */
    abstract protected function execute(InputInterface $input, OutputInterface $output);
    abstract protected function getDescription(): string;
    protected function configure(Config $config){}
}