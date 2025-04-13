<?php
namespace Bullsoft\Phplus\App;

use Phalcon\Application\AbstractApplication as AbstractApp;
use Phalcon\Config\Config as PhConfig;
use Bullsoft\Phplus\Enum\RunEnv;
use Bullsoft\Phplus\Sys;
use Bullsoft\Phplus\App\Module\Def as ModuleDef;
use Bullsoft\Phplus\App\Module\AbstractModule;
use Phalcon\Autoload\Loader as PhLoader;
use Phalcon\Di\DiInterface as PhDiInterface;

final class App extends AbstractApp
{
    // 默认运行环境
    // Enum: ['dev', 'test', 'pre-production', 'production']
    protected $env = RunEnv::DEV;
    // Booted flag
    protected $booted = false;
    // 全局配置 <Config>
    // protected PhConfig $config;

    // 处理请求次数
    protected $requestNumber = 0;
    // 需要手动关闭的服务 []callable，一般是有状态服务，如Mysql、Redis等
    protected array $finalizers = [];

    public function __construct(protected PhConfig $config) {}

    public function boot(RunEnv $env): static
    {
        $this->env = $env;
        // define global constants
        define("APP_RUN_ENV", $this->env->value);
        define("APP_ROOT_DIR", Sys::getRootDir());
        define("APP_PRI_MODULE_DIR", Sys::getPrimaryModuleDir());
        define("APP_ROOT_COMMON_DIR", Sys::getCommonDir());
        define("APP_ROOT_COMMON_LOAD_DIR", Sys::getGlobalLoadDir());
        define("APP_ROOT_COMMON_CONF_DIR", Sys::getGlobalConfigDir());
        define("PHPLUS_DIR", Sys::getLibraryDir());

        return $this->bootPrimaryModule();
    }

    private function bootPrimaryModule(): static
    {
        $primaryModuelDef = new ModuleDef(Sys::getPrimaryModuleDir(), true);
        $di = $primaryModuelDef->newDi();
        $di->setShared("app", $this);
        $di->setShared("config", $this->config);
        $di->setShared("loader", new PhLoader());
        $this->eventsManager = $di->get("eventsManager");
        $this->setDI($di);

        $this->booted = true;
        $this->registerModule($primaryModuelDef);
        return $this;
    }

    public function registerModule(ModuleDef $def): AbstractModule
    {
        if (isset($this->modules[$def->name()])) {
            return $this->modules[$def->name()];
        }
        if ($def->isPrimary()) {
            if (!defined("APP_RUN_MODE")) {
                define("APP_RUN_MODE", $def->getModeValue(), false);
            }
            // 合并主模块的配置
            $this->config->merge($def->getConfig());
            $this->setDefaultModule($def->name());
        }
        $moduleObj = $def->checkout();
        $this->modules[$def->name()] = $moduleObj;
        return $moduleObj;
    }

    public function getModule(string $name)
    {
        return $this->modules[$name] ?? false;
    }

    // 传入的 Config 优先级更高
    public function setConfig(PhConfig $config): static
    {
        $this->config->merge($config);
        return $this;
    }

    public function config(): PhConfig
    {
        return $this->config;
    }

    public function env(): RunEnv
    {
        return $this->env;
    }

    public function di(): PhDiInterface
    {
        return $this->container;
    }

    public function isBooted(): bool
    {
        return $this->booted === true;
    }

    public function handle()
    {
        $this->requestNumber++;
        if (false === $this->booted) {
            $this->bootPrimaryModule();
        }

        $response = null;
        $params = func_get_args();
        $eventManager = $this->eventsManager;

        $defaultModuleObj = $this->modules[$this->defaultModule];

        if (
            $eventManager->fire("app:beforeExecModule", $this, [
                $defaultModuleObj,
                $params,
            ]) === false
        ) {
            return false;
        }

        $response = $defaultModuleObj->exec($params);
        if (
            $eventManager->fire("app:afterExecModule", $this, [
                $defaultModuleObj,
                $response,
            ])
        ) {
            return false;
        }

        return $response;
    }

    public function defer(callable $handler): static
    {
        $this->finalizers[] = $handler;
        return $this;
    }

    public function terminate(bool $deeply = true)
    {
        // Close Session here
        if (session_status() == \PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        // Empty Session-Id
        if (!headers_sent()) {
            session_id("");
        }

        // Reset global variables
        $_SESSION = [];
        $_POST = [];
        $_GET = [];
        $_SERVER = [];
        $_REQUEST = [];
        $_COOKIE = [];
        $_FILES = [];

        foreach ($this->modules as $_ => $moduleObj) {
            unset($moduleObj);
        }
        $this->defaultModule = "";
        $this->booted = false;

        foreach ($this->finalizers as $finalizer) {
            $finalizer();
        }
        $this->finalizers = [];

        // Clear request number
        $this->requestNumber = 0;
        // Reset Dependency Injector
        if ($deeply) {
            $this->di()->reset();
            $this->container = null;
        }
    }

    public function getRequestNumber(): int
    {
        return $this->requestNumber;
    }

    public function getPrimaryModule(): AbstractModule
    {
        return $this->getModule($this->defaultModule);
    }

    public function __call(string $method, array $params)
    {
        if (!$this->isBooted()) {
            return;
        }
        if ($this->container->has($method)) {
            return $this->container->get($method, $params);
        }
        return;
    }
}
