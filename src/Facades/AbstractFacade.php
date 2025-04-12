<?php

namespace Bullsoft\Phplus\Facades;

use Phalcon\Di\DiInterface;
use Bullsoft\Phplus\App\App;
use Bullsoft\Phplus\Exception\Base as BaseException;
use Bullsoft\Phplus\Sys;

abstract class AbstractFacade
{
    abstract protected function getName(): string;

    protected function resovle(DiInterface $di): mixed
    {
        return null;
    }

    public static function setApp(App $app)
    {

    }

    public static function getApp(): App
    {
        return Sys::app();
    }

    public static function app(): App
    {
        return Sys::app();
    }

    public static function itself()
    {
        $di = Sys::app()->di();
        $name = get_called_class();
        $facade = new $name();
        $service = null;
        if ($di->has($facade->getName())) {
            $service = $di->get($facade->getName());
        } else {
            $service = $facade->resolve($di);
        }
		
        if (null === $service) {
            trigger_error("Service can not be resovled: " . $name);
            throw new BaseException("Service can not be resovled: " . $name);
        }
        
		return $service;
	}

    public static function __callStatic(string $method, array $params)
    {
        $service = static::itself();
		
        return call_user_func_array(
            [$service, $method], 
            $params
        );
    }
}
