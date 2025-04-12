<?php
namespace Bullsoft\Phplus;
use Bullsoft\Phplus\App\App;
use Bullsoft\Phplus\Exception\Base as BaseException;
use Phalcon\Config\Config as PhConfig;
use Bullsoft\Phplus\Facades\Facade;
use Exception as PhpException;

final class Sys
{
    // 定义类常量
    public const COMMON_NAME = "common";
    public const CONF_NAME   = "config";
    public const LOAD_NAME   = "load";
    public const PUB_NAME    = "public";
    public const APP_NAME    = "app";

    public const DS = \DIRECTORY_SEPARATOR;
    public const EXT = ".php";
    public const ENV_NAME = "phplus.env";

    private static string $rootDir = "";  // without trailing /
    private static string $primaryModuleDir = ""; // without trailing /

    private static array $requiredFiles = [];
    private static ?App $app = null;

    public static function init(string $moduleDir): void
    {
        if(!empty(self::$primaryModuleDir)) {
            return ;
        }
        $moduleDir = rtrim($moduleDir, Sys::DS);
        if(!is_dir($moduleDir)) {
            throw new BaseException("Module directory not exists or not a dir, file positon: " . $moduleDir);
        }
        self::$primaryModuleDir = $moduleDir;
        self::$rootDir = dirname($moduleDir);
    }

    public static function initConfig(): PhConfig
    {
        $globalConfigPath = Sys::getGlobalConfigPath();
        $globalConfig = null;
        try {
            $globalConfig = new PhConfig(Sys::load($globalConfigPath));
        } catch(PhpException $e) {
            $globalConfig = new PhConfig([]);
            trigger_error("Global config file not exists: " . $e->getMessage());
        }
        return $globalConfig;
    }

    public static function start(): App
    {
        // Initial only once
        if(self::$app === null) {
            self::$app = new App(Sys::initConfig());
        }
        // 加载Facacdes
        Facade::register();
        return self::$app;
    }

    public static function app(): App
    {
        if(self::$app == null) {
            throw new PhpException("SuperApp has no instances yet");
        }
        return self::$app;
    }

    // -> {APP_MODULE_DIR}
    public static function getPrimaryModuleDir(): string
    {
        return self::$primaryModuleDir;
    }

    // -> {APP_ROOT_DIR}
    public static function getRootDir(): string
    {
        return self::$rootDir;
    }

    // -> {APP_ROOT_DIR}/common
    public static function getCommonDir(): string
    {
        return implode(Sys::DS, [
            self::$rootDir,
            Sys::COMMON_NAME
        ]);
    }

    // -> {APP_ROOT_DIR}/common/config
    public static function getGlobalConfigDir(): string
    {
        return implode(Sys::DS, [
            self::$rootDir,
            Sys::COMMON_NAME,
            Sys::CONF_NAME
        ]);
    }

    // -> {APP_ROOT_DIR}/common/config/config.php
    public static function getGlobalConfigPath(): string
    {
        return implode(Sys::DS, [
            self::$rootDir,
            Sys::COMMON_NAME,
            Sys::CONF_NAME,
            Sys::CONF_NAME . Sys::EXT
        ]);
    }

    // -> {APP_ROOT_DIR}/common/load
    public static function getGlobalLoadDir(): string
    {
        return implode(Sys::DS, [
            self::$rootDir,
            Sys::COMMON_NAME,
            Sys::LOAD_NAME
        ]);
    }

    // -> {APP_ROOT_DIR}/{moduleName}
    public static function getModuleDirByName(string $moduleName): string
    {
        return implode(Sys::DS, [
            self::$rootDir,
            $moduleName
        ]);
    }

    // foo/bar/baz -> baz
    public static function getModuleNameByDir(string $moduleDir): string
    {
        return pathinfo($moduleDir, \PATHINFO_FILENAME);
    }

    // {moduleDir}/app/{modeName}.php
    public static function getModuleClassPath(string $moduleDir, string $modeName)
    {
        return implode(Sys::DS, [
            $moduleDir,
            Sys::APP_NAME,
            $modeName . Sys::EXT
        ]);
    }

    // -> {moduleDir}/app/config/{APP_RUN_ENV | config}.php
    public static function getModuleConfigPath(string $moduleDir)
    {
        $confPath = "";
        $confPath = implode(Sys::DS, [
            $moduleDir,
            Sys::APP_NAME,
            Sys::CONF_NAME,
            APP_RUN_ENV . Sys::EXT
        ]);
        if(!is_file($confPath)) {
            $confPath = implode(Sys::DS, [
                $moduleDir,
                Sys::APP_NAME,
                Sys::CONF_NAME,
                Sys::CONF_NAME . Sys::EXT
            ]);
        }
        if(!is_file($confPath)) {
            throw new BaseException("Module Config file not exists: " . $confPath . " & " . APP_RUN_ENV . self::EXT);
        }
        return $confPath;
    }

    // -> {APP_ROOT_DIR}/vendor/autoload.php
    public static function getComposerAutoloadPath()
    {
        return implode(Sys::DS, [
            self::$rootDir,
            "vendor",
            "autoload.php"
        ]);
    }

    public static function load(string $filePath, array $context = [])
    {
        if(!is_file($filePath)) {
            throw new PhpException("The file you try to load is not exists. The Path is: " . filePath);
        }

        if(in_array($filePath, get_included_files()))  return ;

        // root, config, superapp, di, loader
        $root = Sys::getRootDir();
        if(null !== self::$app) {
            $config = self::$app->config();
            if(self::$app->isBooted()) {
                $app = self::$app;
                $di = $app->di();
                $loader = $di->get("loader");
            }
        }

        extract($context);

        // Require file and hold the result for subsequent request.
        return include_once $filePath;
    }

    public static function shutdown()
    {
        unset(self::$requiredFiles);
        self::$app->terminate();
        unset(self::$app);
    }
}
