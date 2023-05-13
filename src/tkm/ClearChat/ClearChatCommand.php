<?php

namespace tkm\ClearChat;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

class ClearChatCommand extends Command implements PluginOwned {

    public function __construct()
    {
        parent::__construct("clearchat", Main::getInstance()->getConfig()->get("description"), "", Main::getInstance()->getConfig()->get("aliases", []));
        $this->setPermission("clearchat.cmd");
        $this->setPermissionMessage(Main::getInstance()->getConfig()->get("no.perm"));
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player)return false;
        if(!$this->testPermission($sender))return false;
        $this->getOwningPlugin()->clear(false, $sender);
    }

    public function getOwningPlugin(): Plugin
    {
        return Main::getInstance();
    }

    public function getOwningPlugin(): Plugin
    {
        return Main::getInstance();
    }
}