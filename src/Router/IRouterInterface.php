<?php
declare(strict_types=1);

namespace EasySwooleApi\Core\Router;

use FastRoute\RouteCollector;

interface IRouterInterface
{
    public function register(RouteCollector &$routeCollector): void;
}
