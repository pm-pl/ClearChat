<?php

namespace tkm\ClearChat;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase implements Listener {
    use SingletonTrait;

    public array $messages;

    protected function onEnable(): void
    {
        self::setInstance($this);
        self::saveDefaultConfig();
        $this->messages = array();
        if(self::getConfig()->get("autocleartype") === "auto"){
            self::getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(function ():void{
                $this->clear(true);
            }), self::getConfig()->get("autocleardelay")*1200, self::getConfig()->get("autocleardelay")*1200);
        }
        Server::getInstance()->getPluginManager()->registerEvents(self::getInstance(), self::getInstance());
        Server::getInstance()->getCommandMap()->register(self::getName(), new ClearChatCommand());
    }

    public function onChat(PlayerChatEvent $event){
        if(self::getConfig()->get("autocleartype") === "messages"){
            if(count($this->messages) === self::getConfig()->get("autoclearcount")){
                $this->messages = [];
                $this->clear(true);
            }else{
                $this->messages[] = $event->getMessage();
            }
        }
    }

    public function clear(bool $auto = false, Player $player = null){
        foreach (Server::getInstance()->getOnlinePlayers() as $oplayer){
            if(!$oplayer->hasPermission("clearchat.bypass")){
                $oplayer->sendMessage(str_repeat("\n", 255));
            }
        }
        if(!$auto and !is_null($player)){
            Server::getInstance()->broadcastMessage(str_replace("{player}", $player->getName(), self::getInstance()->getConfig()->get("player.cleared")));
        }else{
            Server::getInstance()->broadcastMessage(self::getInstance()->getConfig()->get("auto.cleared"));
        }
    }

}