<?php
declare(strict_types=1);

namespace EasySwooleApi\Core\Redis;

use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\Redis\Redis;
use EasySwoole\RedisPool\RedisPool;
use Swoole\Coroutine\Scheduler;
use Swoole\Timer;

class RedisManager
{
    public static function checkESRedisPool(string $name, array $config)
    {
        $success   = false;
        $error     = '';
        $scheduler = new Scheduler();
        $scheduler->add(function () use ($config, &$success, &$error) {
            $redisConfigObj = new RedisConfig($config);
            $client         = new Redis($redisConfigObj);
            $ret            = $client->connect();
            if ($ret) {
                $success = true;
            } else {
                $error = "connection fail.";
            }
        });
        $scheduler->start();
        Timer::clearAll();
        if ($success) {
            return true;
        } else {
            throw new \Exception("EasySwoole Redis Pool [{$name}] configuration error: " . $error);
        }
    }

    public static function initESRedisPool()
    {
        $redisConfig = \config('redis');
        foreach ($redisConfig as $name => $config) {
            self::checkESRedisPool($name, $config);
            $redisConfigObj = new RedisConfig($config);
            RedisPool::getInstance()->register($redisConfigObj, $name);
        }
    }
}
