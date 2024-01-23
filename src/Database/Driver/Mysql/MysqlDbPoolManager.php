<?php
declare(strict_types=1);

namespace EasySwooleApi\Core\Database\Driver\Mysql;

use EasySwoole\Component\Singleton;
use EasySwoole\ORM\DbManager;
use EasySwooleApi\Core\Database\Driver\IDbPoolManagerInterface;
use EasySwooleApi\Core\Database\Driver\Mysql\Driver\ESOrmPool;

class MysqlDbPoolManager implements IDbPoolManagerInterface
{
    use Singleton;

    public function registerDbPool(string $name, array $config): void
    {
        $driverClass = $config['driverClass'];
        if ($driverClass === DbManager::class) {
            ESOrmPool::getInstance()->initPool($name, $config);
        }
    }

    public function keepMin(string $name, array $config)
    {
        $driverClass = $config['driverClass'];
        if ($driverClass === DbManager::class) {
            ESOrmPool::getInstance()->keepMin($name, $config);
        }
    }
}
