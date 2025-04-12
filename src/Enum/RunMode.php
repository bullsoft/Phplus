<?php

namespace Bullsoft\Phplus\Enum;
use Bullsoft\Phplus\Sys;

enum RunMode: string
{
    case WEB = 'Web';
    case CLI = 'Cli';
    case SRV = 'Srv';
    case MICRO = 'Micro';

    protected const SCRIPTS = [
        "Web"   => "/default-web.php",
        "Cli"   => "/default-cli.php",
        "Srv"   => "/default-web.php",
        "Micro" => "/default-micro.php",
    ];

    public const __default = self::CLI;

    public static function getDefault() 
    {
        return self::CLI;
    }

    public function getScriptPath(): ?string
    {
        return Sys::getGlobalLoadDir().self::SCRIPTS[$this->value];
    }

    public function isCli(): bool
    {
        return $this == self::CLI;
    }

    public function isWeb(): bool
    {
        return $this == self::WEB;
    }

    public function isSrv(): bool
    {
        return $this == self::SRV;
    }

    public function isMicro(): bool 
    {
        return $this == self::MICRO;
    }

    public function getMapClassName(): string
    {
        return $this->getClass();
    }

    public function getClass(): string
    {
        return $this->value;
    }
}