<?php
declare(strict_types=1);
if (!function_exists('config')) {
    /**
     * @param string $keyPath 配置的key 支持多级
     *                        如 MYSQL.default.host
     *
     * @return array|mixed|null
     */
    function config(string $keyPath = '')
    {
        return \EasySwoole\EasySwoole\Config::getInstance()->getConf($keyPath);
    }
}

if (!function_exists('container')) {
    /**
     * @return Closure|\EasySwooleApi\Core\Ioc\Container
     */
    function container()
    {
        return \EasySwooleApi\Core\Ioc\Container::getInstance();
    }
}

if (!function_exists('bind')) {
    /**
     * 绑定一个类到容器
     *
     * @param string|array $abstract 类标识、接口（支持批量绑定）
     * @param mixed        $concrete 要绑定的类、闭包或者实例
     *
     * @return \EasySwooleApi\Core\Ioc\Container
     */
    function bind($abstract, $concrete = null)
    {
        return \EasySwooleApi\Core\Ioc\Container::getInstance()->bind($abstract, $concrete);
    }
}

if (!function_exists('invoke')) {
    /**
     * 调用反射实例化对象或者执行方法 支持依赖注入
     *
     * @param mixed $call 类名或者callable
     * @param array $args 参数
     *
     * @return mixed
     */
    function invoke($call, array $args = [])
    {
        if (is_callable($call)) {
            return \EasySwooleApi\Core\Ioc\Container::getInstance()->invoke($call, $args);
        }

        return \EasySwooleApi\Core\Ioc\Container::getInstance()->invokeClass($call, $args);
    }
}

if (!function_exists('es_request')) {
    /**
     * 获取当前 EasySwoole Request 对象实例
     *
     * @return \EasySwoole\Http\Request
     */
    function es_request()
    {
        return \EasySwooleApi\Core\Context\ContextUtil::get(\EasySwooleApi\Core\Constant\CoreConstant::ES_HTTP_REQUEST);
    }
}

if (!function_exists('request')) {
    /**
     * 获取当前 Request 对象实例
     *
     * @return \EasySwooleApi\Core\Request\Request
     */
    function request()
    {
        return new \EasySwooleApi\Core\Request\Request(es_request());
    }
}

if (!function_exists('es_response')) {
    /**
     * 获取当前 EasySwoole Response 对象实例
     *
     * @return \EasySwoole\Http\Response
     */
    function es_response()
    {
        return \EasySwooleApi\Core\Context\ContextUtil::get(\EasySwooleApi\Core\Constant\CoreConstant::ES_HTTP_RESPONSE);
    }
}

if (!function_exists('response')) {
    /**
     * 发送普通数据给客户端
     *
     * @param mixed      $data   输出数据
     * @param int|string $code   状态码
     * @param array      $header 头信息
     * @param string     $type
     *
     * @return false|mixed
     */
    function response($data = [], int $code = 200, array $header = [], string $type = 'html')
    {
        return \EasySwooleApi\Core\Response\Response::create(es_response(), $data, $type, $code, $header)->send();
    }
}

if (!function_exists('json')) {
    /**
     * 发送json数据给客户端
     *
     * @param $data
     * @param $code
     * @param $header
     * @param $options
     *
     * @return false|mixed
     */
    function json($data = [], $code = 200, $header = [], $options = [])
    {
        return \EasySwooleApi\Core\Response\Response::create(es_response(), $data, 'json', $code, $header, $options)->send();
    }
}

if (!function_exists('redirect')) {
    /**
     * 发送重定向请求给客户端
     *
     * @param $url
     * @param $params
     * @param $code
     * @param $with
     *
     * @return mixed
     */
    function redirect(string $url, array $params = [], int $code = \EasySwoole\Http\Message\Status::CODE_MOVED_TEMPORARILY)
    {
        if (is_integer($params)) {
            $code   = $params;
            $params = [];
        }
        if (!empty($params)) {
            $url = $url . '?' . http_build_query($params);
        }
        return es_response()->redirect($url, $code);
    }
}
