<?php

namespace morskoi\MagicStick\event;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\ItemTypeIds;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\world\particle\DustParticle;
use pocketmine\color\Color;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Living;
use morskoi\MagicStick\MagicStick;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;

class EventListener implements Listener
{
    private MagicStick $plugin;
    private array $cooldowns = [];
    public function __construct(MagicStick $plugin)
    {
        $this->plugin = $plugin;
    }
    public function MagicStickUse(PlayerItemUseEvent $e): void
    {
        $p = $e->getPlayer();
        $item = $e->getItem();
        $nametag = $item->getNamedTag()->getTag("magicstick");

        if ($item->getTypeId() === ItemTypeIds::BLAZE_ROD && $nametag !== null) 
            {
            $pn = $p->getName();
            $currentTime = time();
            $cooldownDuration = 60;

            if (isset($this->cooldowns[$pn]) && $currentTime < $this->cooldowns[$pn]) 
                {
                $timeLeft = $this->cooldowns[$pn] - $currentTime;
                $p->sendMessage("§cMagic Stick in cooldown, §eleft: §6" . $timeLeft . " s.");
                return;
            }

            $this->cooldowns[$pn] = $currentTime + $cooldownDuration;

            $color = new Color(255, 127, 0);
            $pos = $p->getPosition();
            $pk = LevelSoundEventPacket::create(
                LevelSoundEvent::EXPLODE,
                $pos,
                -1,
                ":",
                false,
                false,
                false,
            );
            $p->getNetworkSession()->sendDataPacket($pk);
            $world = $p->getWorld();
            $rad = 5;
            foreach($world->getNearbyEntities($p->getBoundingBox()->expandedCopy($rad, $rad, $rad)) as $entity) 
                {
                if($entity instanceof Living && $entity !== $p) 
                    {
                    $world->addParticle($pos, new DustParticle($color));
                    $entity->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 20 * 5, 1));
                    $entity->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 20 * 5, 1));
                    $entity->setMotion($entity->getMotion()->add(0, 0.5, 0));
                }
            }
        }
    }
}
