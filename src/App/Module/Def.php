<?php
namespace Bullsoft\Phplus\App\Module;

use Phalcon\Di\DiInterface;
use Phalcon\Config\Config as PhConfig;
use Phalcon\Di\FactoryDefault as DefaultDi;
use Phalcon\Di\FactoryDefault\CLI as TaskDi;

use Bullsoft\Phplus\Sys;
use Bullsoft\Phplus\Enum\RunMode;
use Bullsoft\Phplus\Exception\Base as BaseException;
use Phalcon\Di\Injectable as PhDiInjectable;
use \Exception as PhpException;

class Def extends PhDiInjectable
{
    protected string $classPath = "";
    protected string $className = "";
    protected string $name = "";
    protected string $configPath = "";
    protected string $dir = "";

    // <\Phalcon\Config\Config>
    protected PhConfig $config;
    // <Bullsoft\Phplus\Enum\RunMode>
    protected RunMode $runMode;

    // Is this a primary-module? false for default
    protected $isPrimary = false;

    public function __construct(string $moduleDir, bool $isPrimary = false)
    {
        if(!is_dir($moduleDir)) {
            throw new BaseException("Module directory not exists or not a dir, file positon: " . $moduleDir);
        }
        $this->isPrimary = $isPrimary;
        $this->dir = $moduleDir;
        $this->configPath = Sys::getModuleConfigPath($moduleDir);

        // 模块配置
        $this->config = new PhConfig(
            Sys::load($this->configPath, [
                "def" => $this,
            ])
        );
        if(!isset($this->config["application"])) {
            throw new BaseException("Config Path: /application must exists");
        }
        // Application Config
        $appConfig = $this->config->application;
        $this->name = $appConfig->name;

        $this->runMode = RunMode::from($appConfig->mode);

        $this->classPath = Sys::getModuleClassPath($moduleDir, $this->runMode->getClass());
        if(is_file($this->classPath)) {
            $this->className = $appConfig->ns . $this->runMode->getClass();
        } else {
            $this->classPath = Sys::getModuleClassPath($moduleDir, "Module");
            $this->className = $appConfig->ns . "Module";
        }
    }

    public function newDi(): DiInterface
    {
        if(!$this->isPrimary()) {
            throw new BaseException("Only primary module can have DenpendencyInjection");
        }
        if(null !== $this->container) {
            return $this->container;
        }
        if($this->runMode->isCli()) {
            $this->container = new TaskDi();
        } else {
            $this->container = new DefaultDi();
        }
        $this->container->setInternalEventsManager($this->container->get("eventsManager"));
        return $this->container;
    }

    public function di(): ?DiInterface
    {
        return $this->container;
    }

    public function checkout(): AbstractModule
    {
        if($this->isPrimary()) {
            // 装载全局服务初始化文件
            try {
                Sys::load($this->runMode->getScriptPath());
            } catch(BaseException $e) {
                // nothing to do...
                trigger_error("Phplus: Global load scripts not exists: " . $e->getMessage());
            } catch(PhpException $e) {
                trigger_error($e->getMessage());
            }
        }
        Sys::load($this->classPath);
        if(!class_exists($this->className)) {
            throw new BaseException([
                "Module class (%s) not exists, ClassPath: %s ",
                [
                    $this->className,
                    $this->classPath
                ]
            ]);
        }

        $className = $this->className;
        $module = new $className(Sys::app(), $this);

        if($this->isPrimary()) {
            $this->di->setShared("defaultModule", $module);
        }

        // Register autoloaders and di-services
        $module->registerAutoloaders($this->di);
        $module->registerServices($this->di);
        $module->registerEvents($this->di);

        return $module;
    }

    public function isDefault(): bool
    {
        return $this->isPrimary === true;
    }

    public function isWeb(): bool
    {
        return $this->runMode->isWeb();
    }

    public function isCli(): bool
    {
        return $this->runMode->isCli();
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary === true;
    }

    public function getIsPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function classPath(): string
    {
        return $this->classPath;
    }

    public function getClassPath(): string
    {
        return $this->classPath;
    }

    public function className(): string
    {
        return $this->className;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function runMode(): RunMode
    {
        return $this->runMode;
    }

    public function getRunMode(): RunMode
    {
        return $this->runMode;
    }

    public function mapClassName(): string
    {
        return $this->runMode->getClass();
    }

    public function getMapClassName(): string
    {
        return $this->runMode->getClass();
    }

    public function mode(): RunMode
    {
        return $this->runMode;
    }

    public function getMode(): RunMode
    {
        return $this->runMode;
    }

    public function getModeValue(): string
    {
        return $this->runMode->value;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function ns(): string
    {
        return rtrim($this->config->path("application.ns"), "\\");
    }

    public function getNs(): string
    {
        return rtrim($this->config->path("application.ns"), "\\");
    }

    public function configPath(): string
    {
        return $this->configPath;
    }

    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    public function getConfig(): PhConfig
    {
        return $this->config;
    }

    public function dir(): string
    {
        return $this->dir;
    }

    public function getDir(): string
    {
        return $this->dir;
    }

    public function config(): PhConfig
    {
        return $this->config;
    }
}
