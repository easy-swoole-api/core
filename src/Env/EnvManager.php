<?php
declare(strict_types=1);

namespace EasySwooleApi\Core\Env;

use EasySwoole\EasySwoole\Core;

class EnvManager
{
    public const RUN_MODE_DEV = 'dev';
    public const RUN_MODE_TEST = 'test';
    public const RUN_MODE_PRODUCE = 'produce';

    public static function currentRunMode()
    {
        return Core::getInstance()->runMode();
    }

    public static function isDevMode()
    {
        return self::currentRunMode() === self::RUN_MODE_DEV;
    }

    public static function isTestMode()
    {
        return self::currentRunMode() === self::RUN_MODE_TEST;
    }

    public static function isProduceMode()
    {
        return self::currentRunMode() === self::RUN_MODE_PRODUCE;
    }
}
