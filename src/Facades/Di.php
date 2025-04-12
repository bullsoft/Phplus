<?php

namespace Bullsoft\Phplus\Facades;
use Phalcon\Di\DiInterface;

class Di extends AbstractFacade
{
    protected function getName(): string
    {
        return "di";
    }

    protected function resovle(DiInterface $di): mixed {
        return $di;
    }
}