<?php
declare(strict_types=1);

namespace EasySwooleApi\Core\Database\Driver\Mysql\Driver;

interface IMysqlPool
{
    public function checkPool(string $name, array $config): bool;

    public function initPool(string $name, array $config): void;
}
