<?php

namespace App\Fabric\Route;

use App\Fabric\Route\HandleInterface;
use App\Fabric\Route\Process\Request;
use App\Fabric\Route\Process\Response;
use App\Fabric\System\Container\ApiContainer;

abstract class Handle implements HandleInterface
{
    private Request $request;
    private ApiContainer $container;
    public function __construct(Request $request)
    {
        $this->container = new ApiContainer();
        $this->request = $request;
    }

    public function logger(): \Psr\Log\LoggerInterface
    {
        return $this->container->getLogger()->branch(
            str_replace("/", "_", trim($this->request->url()->relativePath(), "/"))
        );
    }

    protected function request(): Request
    {
        return $this->request;
    }

    protected function response(int $status_code = 200): Response
    {
        return new Response($status_code);
    }


    public function prepare(): void
    {
        $r = new \ReflectionClass(static::class);
        foreach($r->getProperties() as $property)
        {
            foreach($property->getAttributes() as $attribute)
            {
                try
                {
                    if(is_a($attribute->getName(), Access::class, true))
                    {
                        /** @var Access $instance */
                        $instance = $attribute->newInstance();
                        $object = $instance->calculate($this->request, $property->getName(), $property->getType()->getName());
                        $property->setValue($this, $object);
                    }
                }catch (\Throwable $e)
                {
                    print_r($e);die;
                }

            }
        }
    }
}