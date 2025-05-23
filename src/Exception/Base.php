<?php

namespace Bullsoft\Phplus\Exception;

use Exception as PhpException;
use Bullsoft\Phplus\Sys;
use Phalcon\Logger\Logger as PhLogger;

class Base extends PhpException
{
    protected int $level = PhLogger::DEBUG;
    protected mixed $info;

    public function __construct($info = null, int $code = 0)
    {
        $args = [];
        $message = "An exception created: " . get_class($this);
        if(!empty($info)) {
            if(is_array($info)) {
                $this->info = $info;
                $message .=  ", message: " . strval($info[0]);
                $args = $info[1]?? ($info["args"]?? []);
                if(!is_array($args)) {
                    $args = [strval($args)];
                }
            } elseif(is_string($info)) {
                $message .= ", message: " . $info;
            }
        }
        try {
            if(Sys::app()->isBooted() && Sys::app()->di()->has("logger")) {
                $logger = Sys::app()->di()->get("logger");
                $argsJson = json_encode($args, \JSON_UNESCAPED_UNICODE);
                $logger->log($this->getLevel(), $message . ", args: ". $argsJson);
            }
        } catch(PhpException $e) {
            // nothing...
        }

        $showMessage = $info["text"] ?? ($this->message ?? $message);

        $cnt = substr_count($showMessage, "%s");
        if($cnt > 0 && count($args) >= $cnt) {
            $showMessage = vsprintf($showMessage, $args);
        }
        if($this->code > 0) {
            $code = $this->code;
        }
        
        parent::__construct($showMessage, $code);
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    public function getInfo()
    {
        return $this->info;
    }

    /**
     * 
     * 
     * $info = "letter A is for Amy";
     * 
     * $info = ["letter A is for Amy", ["Amy"]];
     * 
     * $info = ["letter A is for Amy", 
     *          "text" => "字母A代表%s", 
     *          "args" => ["Amy"]
     *         ];
     * 
     */
    public static function throw($info = "", int $code = 1)
    {
        throw new self($info, $code);
    }
}