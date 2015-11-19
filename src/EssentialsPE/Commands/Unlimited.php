<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseAPI;
use EssentialsPE\BaseFiles\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Unlimited extends BaseCommand{
    /**
     * @param BaseAPI $api
     */
    public function __construct(BaseAPI $api){
        parent::__construct($api, "unlimited", "Allow you to place unlimited blocks", "[player]", true, ["ul", "unl"]);
        $this->setPermission("essentials.unlimited.use");
    }

    /**
     * @param CommandSender $sender
     * @param string $alias
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, $alias, array $args){
        if(!$this->testPermission($sender)){
            return false;
        }
        switch(count($args)){
            case 0:
                if(!$sender instanceof Player){
                    $this->sendUsage($sender, $alias);
                    return false;
                }
                if(($gm = $sender->getServer()->getGamemodeString($sender->getGamemode())) === 1 || $gm === 3){
                    $sender->sendMessage(TextFormat::RED . "[Error] You're in " . $this->getAPI()->getServer()->getGamemodeString($gm) . " mode");
                    return false;
                }
                $this->getAPI()->switchUnlimited($sender);
                $sender->sendMessage(TextFormat::GREEN . "Unlimited placing of blocks " . ($this->getAPI()->isUnlimitedEnabled($sender) ? "enabled!" : "disabled!"));
                break;
            case 1:
                if(!$sender->hasPermission("essentials.unlimited.other")){
                    $sender->sendMessage(TextFormat::RED . $this->getPermissionMessage());
                    return false;
                }
                if(!($player = $this->getAPI()->getPlayer($args[0]))){
                    $sender->sendMessage(TextFormat::RED . "[Error] Player not found");
                    return false;
                }
                if(($gm = $player->getGamemode()) === 1 || $gm === 3){
                    $sender->sendMessage(TextFormat::RED . "[Error] " .  $player->getDisplayName() . " is in " . $this->getAPI()->getServer()->getGamemodeString($gm));
                    return false;
                }
                $this->getAPI()->switchUnlimited($player);
                $sender->sendMessage(TextFormat::GREEN . "Unlimited placing of blocks " . ($this->getAPI()->isUnlimitedEnabled($player) ? "enabled" : "disabled") . " for player " .  $player->getDisplayName());
                $player->sendMessage(TextFormat::GREEN . "Unlimited placing of blocks " . ($this->getAPI()->isUnlimitedEnabled($player) ? "enabled!" : "disabled!"));
                break;
            default:
                $this->sendUsage($sender, $alias);
                return false;
                break;
        }
        return true;
    }
} 