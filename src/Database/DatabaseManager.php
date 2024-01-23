<?php
declare(strict_types=1);

namespace EasySwooleApi\Core\Database;

use EasySwooleApi\Core\Database\Driver\IDbPoolManagerInterface;
use EasySwooleApi\Core\Database\Driver\Mysql\MysqlDbPoolManager;

class DatabaseManager
{
    public static function initDatabasePool()
    {
        $databaseConfig = \config('database');
        foreach ($databaseConfig as $name => $config) {
            if (!empty($config['driver'])) {
                $managerClass = ucfirst($config['driver']) . 'DbPoolManager';
                if ($managerClass === 'MysqlDbPoolManager') {
                    MysqlDbPoolManager::getInstance()->registerDbPool($name, $config);
                }
            }
        }
    }

    public static function setDatabasePoolKeepMin()
    {
        $databaseConfig = \config('database');
        foreach ($databaseConfig as $name => $config) {
            if (!empty($config['driver'])) {
                $managerClass = ucfirst($config['driver']) . 'DbPoolManager';
                if ($managerClass === 'MysqlDbPoolManager') {
                    MysqlDbPoolManager::getInstance()->keepMin($name, $config);
                }
            }
        }
    }
}
