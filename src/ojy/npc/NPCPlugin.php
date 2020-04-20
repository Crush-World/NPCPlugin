<?php

/*
 * //// B1 //// 2020-04-16
 * - NPC
 */

namespace ojy\npc;

use ojy\npc\cmd\NPCCommand;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use ssss\utils\SSSSUtils;

class NPCPlugin extends PluginBase implements Listener
{

    /** @var self|null */
    public static $instance = null;

    public function onLoad()
    {
        self::$instance = $this;
        Entity::registerEntity(NPC::class, true, ['NPC']);
    }

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        foreach ([
                     NPCCommand::class
                 ] as $c)
            Server::getInstance()->getCommandMap()->register('NPCPlugin', new $c);
    }

    public static $queue = [];

    public function onDamage(EntityDamageEvent $event)
    {
        if (!$event instanceof EntityDamageByEntityEvent) return;
        $entity = $event->getEntity();
        if (!$entity instanceof NPC) return;
        $damager = $event->getDamager();
        if (!$damager instanceof Player) return;
        $event->setCancelled();
        if (isset(self::$queue[$damager->getName()])) {
            $data = self::$queue[$damager->getName()];
            $type = $data['type'];
            if ($type === 'id') {
                SSSSUtils::message($damager, '엔티티 ID: ' . $entity->getId());
                unset(self::$queue[$damager->getName()]);
            }
            return;
        }
        $data = $entity->getData();
        $cmd = $data->getCommand();
        if ($cmd !== "" && $cmd !== 'x')
            Server::getInstance()->dispatchCommand($damager, $cmd);
        $message = $data->getMessage();
        if ($message !== "" && $message !== 'x')
            $damager->sendMessage($message);
    }

    public static function createNPC(Player $player, string $name)
    {
        $nbt = Entity::createBaseNBT($player->getPosition(), new Vector3(), $player->yaw, $player->pitch);
        $skin = $player->getSkin();
        $nbt->setTag(new CompoundTag("Skin", [
            new StringTag("Name", $skin->getSkinId()),
            new ByteArrayTag("Data", $skin->getSkinData()),
            new ByteArrayTag("CapeData", $skin->getCapeData()),
            new StringTag("GeometryName", $skin->getGeometryName()),
            new ByteArrayTag("GeometryData", $skin->getGeometryData())
        ]));
        $inventoryTag = new ListTag("Inventory", [], NBT::TAG_Compound);

        $slotCount = $player->getInventory()->getSize() + $player->getInventory()->getHotbarSize();
        for ($slot = $player->getInventory()->getHotbarSize(); $slot < $slotCount; ++$slot) {
            $item = $player->getInventory()->getItem($slot - 9);
            if (!$item->isNull()) {
                $inventoryTag->push($item->nbtSerialize($slot));
            }
        }

        for ($slot = 100; $slot < 104; ++$slot) {
            $item = $player->getArmorInventory()->getItem($slot - 100);
            if (!$item->isNull()) {
                $inventoryTag->push($item->nbtSerialize($slot));
            }
        }

        $nbt->setTag($inventoryTag);

        $nbt->setInt("SelectedInventorySlot", $player->getInventory()->getHeldItemIndex());
        $nbt->setByte('Invulnerable', 1);
        $nbt->setString('NPCData', (new NPCData($name))->serialize());
        $npc = new NPC($player->level, $nbt);
        $npc->setNameTag($name);
        $npc->setNameTagAlwaysVisible();
        $npc->setImmobile();
        $npc->spawnToAll();
    }
}