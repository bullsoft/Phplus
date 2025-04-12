<?php
namespace Bullsoft\Phplus\App\Engine;

use Phalcon\Di\Injectable;
use Phalcon\Application\AbstractApplication as PhAbstractApp;

use Bullsoft\Phplus\App\Module\AbstractModule;
use Bullsoft\Phplus\Exception\Base as BaseException;

class AbstractEngine extends Injectable
{
    protected AbstractModule $module;
    protected ?PhAbstractApp $handler = null;

    public function __construct(AbstractModule $module, ?PhAbstractApp $handler = null)
    {
        $this->module = $module;
        $di = $module->getDI();
        $this->setDI($di);
        if($handler !== null) {
            $handler->setEventsManager($di->get("eventsManager"));
            $this->handler = $handler;
        }
    }

    public function handler(): PhAbstractApp
    {
        return $this->handler;
    }

    // return object | null
    public function getHandler(): PhAbstractApp
    {
        if(empty($this->handler)) {
            throw new BaseException("Sorry, empty cli handler");
        }
        return $this->handler;
    }

    public function setHandler(PhAbstractApp $handler): AbstractEngine
    {
        $this->handler = $handler;
        return $this;
    }
}