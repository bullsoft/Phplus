<?php

namespace PhalconPlus\DevTools\Tasks;

class HelpTask extends BaseTask
{
    private $c;

    public function initialize()
    {
        $this->c = 1;
    }

    public function mainAction()
    {
        $this->cli->br()->info("Phalcon+ 命令行工具 (Ver. ".$this->config->version.")");
        $this->cli->br()->out("命令使用方式：");
        $this->cli->info("  <white>...:". APP_ROOT_DIR . "$</white> ./vendor/bin/phplus command arg1 arg2 arg3");
        $this->cli->br()->out('<yellow>可用命令列表: </yellow>');

        $data = [
            [
                "command" => '<light_green>module:create</light_green>',
                "args" => "-",
                'description' => "引导创建Phalcon+模块",
                'alias' => "create-module",
            ],
            [
                "command" => '<light_green>module:list</light_green>',
                "args" => '-',
                'description' => "Phalcon+模块列表",
                'alias' => "-",
            ],
            [
                "command" => '<light_green>module:install</light_green>',
                "args" => 'arg1: $repoUrl[, arg2: $moduleName]',
                'description' => "安装远程GIT上的Phalcon+模块",
                'alias' => '',
            ],
            [
                "command" => '<light_green>module:clean-all</light_green>',
                "args" => '-',
                'description' => "清除所有模块的垃圾信息",
                'alias' => "-",
            ],
            [
                "command" => '<light_green>module:help</light_green>',
                "args" => '-',
                'description' => "Phalcon+模块工具集帮助手册",
                'alias' => "module",
            ],
            [
                "command" => '<light_green>server:start</light_green>',
                "args" => 'arg1: $moduleName',
                'description' => "使用PHP内置服务器运行Phalcon+模块",
                'alias' => "-",
            ],
            [
                "command" => '<light_green>server:stop</light_green>',
                "args" => 'arg1: $moduleName',
                'description' => "关闭Phalcon+模块服务器",
                'alias' => "-",
            ],
            [
                "command" => '<light_green>server:restart</light_green>',
                "args" => 'arg1: $moduleName',
                'description' => "重启Phalcon+模块服务器",
                'alias' => "-",
            ],
            [
                "command" => '<light_green>server:list</light_green>',
                "args" => '-',
                'description' => "Phalcon+模块服务器列表",
                'alias' => "-",
            ],
            [
                "command" => '<light_green>server:help</light_green>',
                "args" => '-',
                'description' => "Phalcon+模块服务器工具集帮助手册",
                'alias' => "server",
            ],
            [
                "command" => '<light_green>model:create</light_green>',
                "args" => '-',
                'description' => "引导创建Phalcon+ORM模型",
                'alias' => "create-model",
            ],
            [
                "command" => '<light_green>model:list</light_green>',
                "args" => 'arg1: $moduleName',
                'description' => "查看指定模块的Phalcon+模型列表",
                'alias' => "-",
            ],
            [
                "command" => '<light_green>model:find</light_green>',
                "args" => 'arg1: $moduleName, arg2: $modelName[, arg3: $condition]',
                'description' => "查询Phalcon+模型的数据列表",
                'alias' => "-",
            ],
            [
                "command" => '<light_green>model:help</light_green>',
                "args" => '-',
                'description' => "查看Phalcon+模型工具集帮助文档",
                'alias' => "model",
            ],
            [
                "command" => '<light_green>exception:create</light_green>',
                "args" => '-',
                'description' => "引导创建Phalcon+异常类",
                'alias' => "create-exception",
            ],
            [
                "command" => '<light_green>rpc:call</light_green>',
                "args" => 'arg1: $moduleName, arg2: $service="A::main"[, arg3: $jsonArgs]',
                'description' => "Phalcon+本地RPC调用",
                'alias' => "-",
            ],
            [
                "command" => '<light_green>help</light_green>',
                "args" => '-',
                'description' => "查看Phalcon+命令行工具帮助文档",
                'alias' => "-",
            ],

        ];

        $this->cli->table($data);

    }
}
