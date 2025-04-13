<?php

namespace Bullsoft\Phplus;
use Exception as PhpException;

use Bullsoft\Phplus\Enum\RunEnv;
use Bullsoft\Phplus\App\App;

final class Bootstrap
{
    public function __construct(string $moduleDir, string $env = "")
    {
        Sys::init($moduleDir);
        try {
            Sys::load(Sys::getComposerAutoloadPath());
        } catch (PhpException $e) {
            // nothing to do
        }
        $runEnv =
            RunEnv::tryFrom($env) ??
            (RunEnv::tryFrom(trim(get_cfg_var(Sys::ENV_NAME))) ??
                RunEnv::getDefault());

        Sys::start()->boot($runEnv);
    }

    public function app(): App
    {
        return Sys::app();
    }

    public function terminate()
    {
        Sys::shutdown();
    }
}
