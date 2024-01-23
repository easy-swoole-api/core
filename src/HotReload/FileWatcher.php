<?php
declare(strict_types=1);

namespace EasySwooleApi\Core\HotReload;

use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\FileWatcher\FileWatcher as ESFileWatcher;
use EasySwoole\FileWatcher\WatchRule;
use EasySwooleApi\Core\Env\EnvManager;

class FileWatcher
{
    public static function enableFileWatcher()
    {
        $fileWatcher = \config('fileWatcher');
        if (!empty($fileWatcher['enable'])) {
            $allowModes = $fileWatcher['allow_mode'] ?? [];
            if (in_array(EnvManager::currentRunMode(), $allowModes)) {
                if (!empty($fileWatcher['monitor_dir']) && is_dir($fileWatcher['monitor_dir'])) {
                    $watcher = new ESFileWatcher();
                    $rule    = new WatchRule($fileWatcher['monitor_dir']);
                    $watcher->addRule($rule);
                    $onChangeHandler = [self::class, 'onChange'];
                    if (!empty($fileWatcher['on_change_handler']) && is_callable($fileWatcher['on_change_handler'])) {
                        $onChangeHandler = $fileWatcher['on_change_handler'];
                    }
                    $watcher->setOnChange($onChangeHandler);
                    $watcher->attachServer(ServerManager::getInstance()->getSwooleServer());
                }
            }
        }
    }

    public static function onChange()
    {
        Logger::getInstance()->info('file change ,reload!!!');
        ServerManager::getInstance()->getSwooleServer()->reload();
    }
}
