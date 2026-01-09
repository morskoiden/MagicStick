<?php

namespace morskoi\MagicStick\commands;

use pocketmine\command\{Command, CommandSender};
use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use morskoi\MagicStick\MagicStick;

class MagicStickCommand extends Command
{
    private MagicStick $plugin;

    public function __construct(MagicStick $plugin)
    {
        parent::__construct("magicstick", "give magic stick");
        $this->setPermission("magicstick.cmd");
        $this->plugin = $plugin;
    }
    public function execute(CommandSender $s, string $label, array $args)
    {
        if (!$s instanceof Player)
        {
            $s->sendMessage("Only in game");
            return;
        }
        $item = VanillaItems::BLAZE_ROD();
        $cfg = $this->plugin->getConfig();
        $name = $cfg->get("item-name", "Magic Stick");
        $lore = $cfg->get("item-lore", ["§bWhen used, it throws enemies into the air\nand applies a slow and blind effect to them.\n§eCooldown: §660 s."]);
        $item->setCustomName($name);
        $item->setLore($lore);
        $item->getNamedTag()->setString("magicstick", true);
        $s->getInventory()->addItem($item);
        $s->sendMessage("§8[§bMagicStick§8] §fThe magic stick is in your inventory.");
    }
}