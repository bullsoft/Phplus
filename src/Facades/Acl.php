<?php

namespace Bullsoft\Phplus\Facades;
use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Acl\Enum as AclEnum;
use Phalcon\Di\DiInterface;
class Acl extends AbstractFacade
{
    protected function getName(): string
    {
        return "acl";
    }

    protected function resovle(DiInterface $di): mixed
    {
        $acl = new AclList();
        $acl->setDefaultAction(AclEnum::DENY);
        $di->setShared($this->getName(), $acl);
        return $acl;
    }
}