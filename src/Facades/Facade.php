<?php

namespace Bullsoft\Phplus\Facades;

enum Facade: string
{
//     case ANNO      = "Annotations";
//     case ASSETS    = "Assets";
    case CONFIG    = "Config";
//     case COOKIES   = "Cookies";
//     case CRYPT     = "Crypt";
//     case ESCAPER   = "Escaper";
//     case EVENT_MGR = "EventsManager";
//     case FILTER    = "Filter";
//     case FLASH     = "Flash";
//
//     case APP_MODULE      = "AppModule";
//     case APP_ENGINE      = "AppEngine";
//     case DISPATCHER      = "Dispatcher";
//     case FLASH_SESSION   = "FlashSession";
//     case MODELS_CACHE    = "ModelsCache";
//     case MODELS_MGR      = "ModelsManager";
//     case MODELS_METADATA = "ModelsMetadata";
//     case SESSION_BAG     = "SessionBag";
//     case SESSION  = "Session";
//     case REQUEST  = "Request";
//     case RESPONSE = "Response";
//     case ROUTER   = "Router";
//     case SECURITY = "Security";
//     case SERVICE  = "Service";
//     case TX_MGR   = "TransactionManager";
//     case REDIS    = "Redis";
//
//     case TAG  = "Tag";
//     case LOG  = "Log";
//     case URL  = "Url";
    case ACL  = "Acl";
    case APP  = "App";
    case DI   = "Di";
//     case VIEW = "View";
//     case USER = "User";

    public static function values(): array
    {
       return array_column(self::cases(), 'value');
    }

    public static function register(): void
    {
        static $loaded = 1;
        if($loaded !== 1) {
            return ;
        }
        $facades = self::values();
        foreach($facades as $alias) {
            $className = __NAMESPACE__."\\".$alias;
            // With Ph prefix - will remove in future
            class_alias($className, "Ph\\{$alias}");
            // With Plus prefix
            class_alias($className, "Phplus\\{$alias}");
        }
        ++$loaded;
        return ;
    }
}