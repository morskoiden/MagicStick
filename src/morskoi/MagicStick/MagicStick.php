<?php

namespace morskoi\MagicStick;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use morskoi\MagicStick\commands\MagicStickCommand;
use morskoi\MagicStick\event\EventListener;

class MagicStick extends PluginBase
{
    private Config $cfg;
    public function onEnable(): void 
    {
        $this->saveResource("config.yml");
        $this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        $this->getServer()->getCommandMap()->register("magicstaff", new MagicStickCommand($this));

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }
    public function getConfig(): Config 
    {
        return $this->cfg;
    }
}