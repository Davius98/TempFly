<?php
namespace Fly;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\utils\TextFormat;

class Main extends PluginBase{
  public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
    if(strtolower($cmd->getName()) == "fly"){
      if(!isset($args[0])){
        $sender->sendMessage(TextFormat::RED . "Uso: /fly [tiempo en segundos] [jugador]");
        return true;
      }
      $time = intval($args[0]);
      if($time <= 0){
        $sender->sendMessage(TextFormat::RED . "Tiempo debe ser un número positivo.");
        return true;
      }
      $player = null;
      if(isset($args[1])){
        $player = $sender->getServer()->getPlayer($args[1]);
        if($player === null){
          $sender->sendMessage(TextFormat::RED . "Jugador no encontrado.");
          return true;
        }
      }else{
        if(!$sender instanceof Entity){
          $sender->sendMessage(TextFormat::RED . "Sólo jugadores pueden volar.");
          return true;
        }
        $player = $sender;
      }
      $player->setAllowFlight(true);
      $player->setFlying(true);
      $sender->getServer()->getScheduler()->scheduleDelayedTask(new class($player) extends Task{
        private $player;
        public function __construct(Entity $player){
          $this->player = $player;
        }
        public function onRun(int $currentTick){
          $this->player->setAllowFlight(false);
          $this->player->setFlying(false);
        }
      }, $time * 20);
      $sender->sendMessage(TextFormat::GREEN . "El jugador " . $player->getName() . " ahora está volando durante " . $time . " segundos.");
    }
    return true;
  }
}


