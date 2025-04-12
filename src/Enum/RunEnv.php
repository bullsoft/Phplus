<?php
namespace Bullsoft\Phplus\Enum;

enum RunEnv: string {
    case DEV = "dev";
    case DEBUG = "debug";
    case TEST = "test";
    case UAT = "uat";
    case PRE_PRODUCTION = "pre_production";
    case AB_TEST = "ab_test";
    case PRODUCTION = "production";

    public const __default = self::DEV;

    public static function getDefault(): RunEnv 
    {
        return self::DEV;
    }

    public static function getDefaultValue(): string
    {
        return self::DEV->value;
    }

    public function isInProd(): bool {
        return $this == self::PRODUCTION;
    }
}