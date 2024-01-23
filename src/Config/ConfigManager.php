<?php
declare(strict_types=1);

namespace EasySwooleApi\Core\Config;

use EasySwoole\EasySwoole\Config;
use EasySwoole\Utility\File;
use EasySwooleApi\Core\Constant\CoreConstant;

class ConfigManager
{
    public static function loadExtraConfig()
    {
        $dirFiles = File::scanDirectory(EASYSWOOLE_ROOT . CoreConstant::DS . 'Config');
        foreach ($dirFiles['files'] as $filePath) {
            if (file_exists($filePath)) {
                $name     = pathinfo($filePath, PATHINFO_FILENAME);
                $confData = require_once $filePath;
                if (!is_array($confData)) {
                    continue;
                }
                Config::getInstance()->merge([$name => $confData]);
            }
        }
    }
}
