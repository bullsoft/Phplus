<?php
namespace PhalconPlus\DevTools;
use Bullsoft\Phplus\App\Module\AbstractModule;
use Phalcon\Di\DiInterface;

use PhalconPlus\DevTools\Providers\ServiceProvider;
use Ph\{Di, Config, App, Sys, };

class Cli extends AbstractModule
{
    public function registerAutoloaders(?DiInterface $di = null)
    {
        $di->get('loader')->setNamespaces(array(
            __NAMESPACE__.'\\Tasks'     => __DIR__.'/tasks/',
            __NAMESPACE__."\\Library"   => __DIR__.'/library/',
            __NAMESPACE__."\\Providers" => __DIR__.'/providers/',
        ), true)->register();
    }

    public function registerServices(?DiInterface $di = null)
    {
        $di->register(new ServiceProvider($this));
    }
}

