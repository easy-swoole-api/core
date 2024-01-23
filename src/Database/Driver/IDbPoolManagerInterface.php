<?php
declare(strict_types=1);

namespace EasySwooleApi\Core\Database\Driver;

interface IDbPoolManagerInterface
{
    public function registerDbPool(string $name, array $config): void;
}
