<?php
declare(strict_types=1);

namespace EasySwooleApi\Core\Event;

use EasySwoole\Command\CommandManager;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Core;
use EasySwoole\EasySwoole\SysConst;
use EasySwoole\EasySwoole\Trigger;
use EasySwoole\Http\Message\Status;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwooleApi\Core\CommandLine\ArgvArgcParser;
use EasySwooleApi\Core\Config\ConfigManager;
use EasySwooleApi\Core\Constant\CoreConstant;
use EasySwooleApi\Core\Context\ContextUtil;
use EasySwooleApi\Core\Crontab\CrontabManager;
use EasySwooleApi\Core\Database\DatabaseManager;
use EasySwooleApi\Core\Env\EnvManager;
use EasySwooleApi\Core\HotReload\FileWatcher;
use EasySwooleApi\Core\Log\LogHandler;
use EasySwooleApi\Core\Process\ProcessManager;
use EasySwooleApi\Core\Redis\RedisManager;
use EasySwooleApi\Core\Trigger\DefaultTrigger;
use Exception;
use Throwable;
use function config;
use function date_default_timezone_set;

class GlobalEvent
{
    public static function autoSetTimezone()
    {
        $timezone = config('app.default_timezone') ?: date_default_timezone_get();
        date_default_timezone_set($timezone);
    }

    public static function bootstrap($argc, $argv)
    {
        ConfigManager::loadExtraConfig();

        self::autoSetTimezone();

        $whiteCommands = [
            'server',
            'task',
            'crontab',
            'process',
        ];
        $parser        = ArgvArgcParser::getInstance()->init($argc, $argv);
        $command       = $parser->getCaller()->getCommand();
        $mode          = $parser->getCommandManager()->getOpt('mode');
        if ($command && !in_array($command, $whiteCommands)) {
            if ($mode) {
                Core::getInstance()->runMode($mode);
            }
            Core::getInstance()->initialize();
        }

        if ($command === 'server') {
            !defined('IS_SERVER_CLI') && define('IS_SERVER_CLI', true);
        }

        // register command
        $commands = config('command');
        if (empty($commands)) {
            $commands = [];
        }
        foreach ($commands as $command) {
            if (!class_exists($command)) {
                throw new Exception("The class {$command} is not found.");
            }
            CommandManager::getInstance()->addCommand(new $command());
        }
    }

    public static function initialize()
    {
        self::autoSetTimezone();
        self::init();
        DatabaseManager::initDatabasePool();
        RedisManager::initESRedisPool();
        CrontabManager::registerCrontab();
        ProcessManager::registerProcess();
    }

    public static function mainServerCreate()
    {
        FileWatcher::enableFileWatcher();
    }

    public static function init()
    {
        // load extra config
        ConfigManager::loadExtraConfig();

        // init log handler
        $logDir = config('LOG.dir');
        Di::getInstance()->set(SysConst::LOGGER_HANDLER, new LogHandler($logDir));

        // init trigger
        Di::getInstance()->set(SysConst::TRIGGER_HANDLER, new DefaultTrigger());

        // init http exception handler
        Di::getInstance()->set(SysConst::HTTP_EXCEPTION_HANDLER, function (Throwable $throwable, Request $request, Response $response) {
            $response->withStatus(Status::CODE_INTERNAL_SERVER_ERROR);
            if (EnvManager::isDevMode() || EnvManager::isTestMode()) {
                $response->write(nl2br($throwable->getMessage() . "\n" . $throwable->getTraceAsString()));
            } else {
                $response->write('error');
            }
            Trigger::getInstance()->throwable($throwable);
        });
    }

    public static function initHttpGlobalOnRequest(Request $request, Response $response)
    {
        ContextUtil::set(CoreConstant::ES_HTTP_REQUEST, $request);
        ContextUtil::set(CoreConstant::ES_HTTP_RESPONSE, $response);
    }
}
