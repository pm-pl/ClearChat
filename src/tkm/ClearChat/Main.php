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

    public static Config $config;
    public static array $messages;

    protected function onEnable(): void
    {
        self::setInstance($this);
        self::saveResource("config.yml");
        self::$config = self::getConfig();
        self::$messages = array();
        if(self::$config->get("autocleartype") === "auto"){
            self::getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(function ():void{
                self::clear(true);
            }), self::$config->get("autocleardelay")*1200, self::$config->get("autocleardelay")*1200);
        }
        Server::getInstance()->getPluginManager()->registerEvents(self::getInstance(), self::getInstance());
        Server::getInstance()->getCommandMap()->register(self::getName(), new ClearChatCommand());
    }

    public function onChat(PlayerChatEvent $event){
        if(self::$config->get("autocleartype") === "messages"){
            if(count(self::$messages) === self::$config->get("autoclearcount")){
                self::$messages = [];
                self::clear(true);
            }else{
                self::$messages[] = $event->getMessage();
            }
        }
    }

    public static function clear(bool $auto = false, Player $player = null){
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            if(!$player->hasPermission("clearchat.bypass")){
                $player->sendMessage(str_repeat("\n", 255));
            }
        }
        if(!$auto and !is_null($player)){
            Server::getInstance()->broadcastMessage(str_replace("{player}", $player->getName(), self::$config->get("player.cleared")));
        }else{
            Server::getInstance()->broadcastMessage(self::$config->get("auto.cleared"));
        }
    }

}