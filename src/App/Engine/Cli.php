<?php

namespace Bullsoft\Phplus\App\Engine;

use Bullsoft\Phplus\App\Module\AbstractModule as AppModule;

use Phalcon\Di\DiInterface;
use Phalcon\Application\AbstractApplication as AbstractApp;
use Phalcon\Cli\Console as PhTaskHandler;
use Phalcon\Cli\Task as PhCliTask;;

class Cli extends AbstractEngine
{
    public function __construct(AppModule $appModule, ?AbstractApp $handler = null)
    {
        if(null === $handler) {
            $handler = new PhTaskHandler();
        }
        parent::__construct($appModule, $handler);
    }
    
    public function exec(array $argv, ?DiInterface $di = null): PhCliTask
    {
        return $this->handler->handle($argv);
    }
}