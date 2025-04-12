<?php 

namespace Bullsoft\Phplus\App\Module;
use Phalcon\Di\DiInterface;
use Bullsoft\Phplus\App\App;
use Bullsoft\Phplus\App\Engine\AbstractEngine;
use Bullsoft\Phplus\App\Module\Def as ModuleDef;
use Bullsoft\Phplus\Exception\Base as BaseException;
use Bullsoft\Phplus\Sys;
use Phalcon\Http\ResponseInterface as PhHttpResponse;
use Phalcon\Cli\Task as PhCliTask;
use Phalcon\Mvc\ModuleDefinitionInterface as PhModuleDef;
use Phalcon\Di\Injectable as PhDiInjectable;

abstract class AbstractModule extends PhDiInjectable implements PhModuleDef
{
    protected App $app;
    protected ModuleDef $def;

    protected $engine;

    public function __construct(App $app, ModuleDef $def)
    {
        $this->app = $app;
        $this->def = $def;
        $this->container = $app->di();
    }

    public function isPrimary(): bool
    {
        return $this->def->getIsPrimary() === true;
    }

    public function dependOn(string $name, bool $force = true): static
    {
        if(!$this->isPrimary()) {
            throw new BaseException("Only primary module can depend on other modules"); 
        }
        
        if($that = $this->app->getModule($name)) {
            return $that;
        }
        
        $moduleDef = new ModuleDef(Sys::getModuleDirByName($name), false);
        if($force === false) {
            if($moduleDef->config()->path("application.exportable", false) == false) {
                throw new BaseException("{$name} can't be imported as external library.");
            }
        }
        $that = self::$app->registerModule($moduleDef);
        // 主模块配置高于被依赖模块
        $that->config->merge($this->config);
        return $that;
    }

    public function engine(): AbstractEngine
    {
        return $this->engine;
    }

    public function exec(array $params = []): false | PhHttpResponse | PhCliTask
    {
        if(!$this->isPrimary()) {
            throw new BaseException("Only primary module can be executed");
        }
        $eventsManager = $this->container->get("eventsManager");
        $this->registerEngine($this->container);
        if($eventsManager->fire("module:beforeExecEngine", $this, [$this->engine, $params]) === false) {
            return false;
        }
        $result = call_user_func_array(
            [$this->engine, "exec"], 
            $params
        );
        if($eventsManager->fire("module:afterExecEngine", $this, [$this->engine, $result]) === false) {
            return false;
        }
        return $result;
    }

    abstract public function registerAutoloaders(?DiInterface $di = null);
    abstract public function registerServices(?DiInterface $di = null);
    public function registerEvents(?DiInterface $di = null): static
    {
        return $this;
    }
    public function registerEngine(?DiInterface $di = null): static
    {
        if(!$this->isPrimary()) {
            return $this;
        }
        if(null === $this->engine) {
            $engineName = $this->getMapClassName();
            $engineClass = "Bullsoft\\Phplus\\App\\Engine\\{$engineName}";
            $this->engine = new $engineClass($this);
        }
        return $this;
    }


    public function __call(string $method, array $params = [])
    {
        return call_user_func_array(
            [$this->def, $method],
            $params
        );
    }
}