<?php
declare(strict_types=1);

namespace EasySwooleApi\Core\Crontab;

use EasySwoole\Crontab\Config;
use EasySwoole\EasySwoole\Crontab\Crontab;
use Error;
use Throwable;
use function config;

class CrontabManager
{
    public static function registerCrontab()
    {
        $crontabConfig = config('crontab');
        if (!$crontabConfig['enable']) {
            return;
        }
        $crontabConfigObj = new Config();
        $crontabConfigObj->setWorkerNum($crontabConfig['worker_num']);
        $onException = [self::class, 'onException'];
        if (!empty($crontabConfig['on_exception']) && !is_callable($crontabConfig['on_exception'])) {
            $onException = $crontabConfig['on_exception'];
        }
        $crontabConfigObj->setOnException($onException);
        $crontabObject  = Crontab::getInstance($crontabConfigObj);
        $crontabClasses = $crontabConfig['crontab'];
        foreach ($crontabClasses as $crontabClass) {
            if (!class_exists($crontabClass)) {
                throw new Error($crontabClass . ' class not exist.');
            }
            $crontabObject->register(new $crontabClass);
        }
    }

    public static function onException(Throwable $throwable)
    {
        throw $throwable;
    }
}
