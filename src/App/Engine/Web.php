<?php

namespace Bullsoft\Phplus\App\Engine;


use Phalcon\Di\Injectable as PhDiInjetable;
use Phalcon\Application\AbstractApplication as PhAbstractApp;
use Phalcon\Mvc\Application as PhMvcHandler;
use Phalcon\Http\ResponseInterface as PhHttpResponse;

use Bullsoft\Phplus\App\App;
use Bullsoft\Phplus\Exception\Base as BaseException;
use Bullsoft\Phplus\Mvc\PsrApplication as PsrHandler;

use Bullsoft\Phplus\App\Module\AbstractModule;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\ServerRequest as GuzzleServerRequest;

use Bullsoft\Phplus\Sys;

class Web extends AbstractEngine
{
    public function __construct(AbstractModule $module, ?PhAbstractApp $handler = null)
    {
        if(null === $handler) {
            $handler = new PhMvcHandler($module->getDI());
        }
        parent::__construct($module, $handler);
    }

    /**
    * @request (for \Phalcon\Mvc\Application) or Psr\Http\Message\Request
    */
    public function exec($request = null): PhHttpResponse
    {
        if($this->handler instanceof PsrHandler) {
            if (is_object($request) && $request instanceof ServerRequestInterface) {
            } else {
                $request = GuzzleServerRequest::fromGlobals();
            }
            return $this->handler->handle($request);
        } elseif($this->handler instanceof PhMvcHandler) {
            $ret = $this->handler->handle(strval($request));
            return $ret;
        }

        throw new BaseException("Handler for Web-Engine must be PsrHandler or MvcHandler");
    }
}