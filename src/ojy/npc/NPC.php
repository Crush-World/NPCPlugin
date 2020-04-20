<?php

namespace ojy\npc;

use pocketmine\entity\Human;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;

class NPC extends Human
{

    public function __construct(Level $level, CompoundTag $nbt)
    {
        parent::__construct($level, $nbt);
    }

    public function initEntity(): void
    {
        parent::initEntity(); // TODO: Change the autogenerated stub
        $data = $this->getData();
        if ($data instanceof NPCData) {
            $this->setNameTag($data->getName());
            $this->setNameTagAlwaysVisible();
        }
    }

    public function getData(): ?NPCData
    {
        $tag = $this->namedtag->getTag('NPCData');
        if ($tag instanceof StringTag) {
            return NPCData::deserialize($tag->getValue());
        }
        return null;
    }

    public function setData(NPCData $data)
    {
        $this->namedtag->setString('NPCData', $data->serialize());
    }

    public function saveNBT(): void
    {
        parent::saveNBT();

        $data = $this->getData();
        if ($data instanceof NPCData) {
            $this->namedtag->setString('NPCData', $data->serialize());
        }
    }
}