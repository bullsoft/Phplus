<?php

namespace Bullsoft\Phplus\Mvc;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

use Phalcon\Http\ResponseInterface as PhResponseInterface;
use Phalcon\Http\Response as PhResponse;
use Phalcon\Di\DiInterface as PhDi;
use Phalcon\Application\AbstractApplication as PhAbstractApp;
use Phalcon\Mvc\Application as PhMvcHandler;

use Bullsoft\Phplus\Exception\Base as BaseException;


class PsrApplication extends PhAbstractApp
{   
    protected PhMvcHandler $handler;

    public function __construct(PhDi $di)
    {
        parent::__construct($di);
        $this->handler = new PhMvcHandler($di);
        $this->handler
             ->sendCookiesOnHandleRequest(false)
             ->sendHeadersOnHandleRequest(false);
    }

    public function handle(ServerRequestInterface $request, boolean $psr = false): PhResponseInterface | PsrResponseInterface
    {
        $uri = "";
        return $this->handler->handle($uri);
    }
}