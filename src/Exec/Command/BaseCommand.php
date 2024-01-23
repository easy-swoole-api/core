<?php
declare(strict_types=1);

namespace EasySwooleApi\Core\Exec\Command;

use EasySwoole\Command\AbstractInterface\CommandHelpInterface;
use EasySwoole\EasySwoole\Command\CommandInterface;

class BaseCommand implements CommandInterface
{
    public function commandName(): string
    {
        $namespaceClassName = static::class;
        $arr                = explode("\\", $namespaceClassName);
        $className          = array_pop($arr);
        return str_replace('Command', '', $className);
    }

    public function exec(): ?string
    {
        return null;
    }

    public function help(CommandHelpInterface $commandHelp): CommandHelpInterface
    {
        return $commandHelp;
    }

    public function desc(): string
    {
        return $this->commandName();
    }
}
