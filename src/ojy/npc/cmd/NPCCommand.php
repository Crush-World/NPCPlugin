<?php

namespace ojy\npc\cmd;

use ojy\npc\NPC;
use ojy\npc\NPCPlugin;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\Server;
use ssss\utils\SSSSUtils;

class NPCCommand extends Command
{

    public function __construct()
    {
        parent::__construct('npc', 'npc 명령어입니다.', '/npc [ id|command|message|name|spawn|remove|scale ]', []);
        $this->setPermission(Permission::DEFAULT_OP);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender->hasPermission($this->getPermission())) {
            if (isset($args[0])) {
                switch ($args[0]) {
                    case 'scale':
                    case 'size':
                        if (isset($args[2])) {
                            $id = intval($args[1]);
                            $scale = floatval($args[2]);
                            if ($scale < 0.4)
                                $scale = 0.4;
                            $npc = Server::getInstance()->findEntity($id);
                            if ($npc instanceof NPC) {
                                $npc->setScale($scale);
                            } else {
                                SSSSUtils::message($sender, "아이디가 {$id}인 NPC를 찾을 수 없습니다.");
                            }
                        } else {
                            SSSSUtils::message($sender, '/npc scale <id> <scale>');
                        }
                        break;
                    case 'remove':
                    case 'delete':
                        if (isset($args[1])) {
                            $id = intval($args[1]);
                            $npc = Server::getInstance()->findEntity($id);
                            if ($npc instanceof NPC) {
                                $npc->getInventory()->clearAll();
                                $npc->getArmorInventory()->clearAll();
                                $npc->kill();
                            } else {
                                SSSSUtils::message($sender, "아이디가 {$id}인 NPC를 찾을 수 없습니다.");
                            }
                        } else {
                            SSSSUtils::message($sender, '/npc remove <id>');
                        }
                        break;
                    case 'create':
                    case 'spawn':
                        if ($sender instanceof Player) {
                            if (isset($args[1])) {
                                unset($args[0]);
                                $name = implode(' ', $args);
                                NPCPlugin::createNPC($sender, $name);
                            } else {
                                SSSSUtils::message($sender, '/npc spawn <name>');
                            }
                        } else {
                            SSSSUtils::message($sender, '인게임에서 실행해주세요.');
                        }
                        break;
                    case 'id':
                        if ($sender instanceof Player) {
                            NPCPlugin::$queue[$sender->getName()] = ['type' => 'id'];
                            SSSSUtils::message($sender, '엔티티 아이디를 볼 NPC를 터치하세요.');
                        } else {
                            SSSSUtils::message($sender, '인게임에서 실행해주세요.');
                        }
                        break;
                    case 'command':
                    case 'cmd':
                        if (isset($args[2])) {
                            $id = intval($args[1]);
                            unset($args[0], $args[1]);
                            $cmd = implode(' ', $args);
                            $npc = Server::getInstance()->findEntity($id);
                            if ($npc instanceof NPC) {
                                $data = $npc->getData();
                                $data->setCommand($cmd);
                                $npc->setData($data);
                                SSSSUtils::message($sender, '명령어 설정을 완료했습니다: ' . $cmd);
                            } else {
                                SSSSUtils::message($sender, "아이디가 {$id}인 NPC를 찾을 수 없습니다.");
                            }
                        } else {
                            SSSSUtils::message($sender, '/npc command <id> <명령어>');
                        }
                        break;
                    case 'message':
                    case 'msg':
                        if (isset($args[2])) {
                            $id = intval($args[1]);
                            unset($args[0], $args[1]);
                            $message = implode(' ', $args);
                            $npc = Server::getInstance()->findEntity($id);
                            if ($npc instanceof NPC) {
                                $data = $npc->getData();
                                $data->setMessage($message);
                                $npc->setData($data);
                                SSSSUtils::message($sender, '메세지 설정을 완료했습니다: ' . $message);
                            } else {
                                SSSSUtils::message($sender, "아이디가 {$id}인 NPC를 찾을 수 없습니다.");
                            }
                        } else {
                            SSSSUtils::message($sender, '/npc message <id> <명령어>');
                        }
                        break;
                    default:
                        SSSSUtils::message($sender, $this->getUsage());
                        break;
                }
            } else {
                SSSSUtils::message($sender, $this->getUsage());
            }
        } else {
            SSSSUtils::prevent($sender, '이 명령어를 사용할 권한이 없습니다.');
        }
    }
}