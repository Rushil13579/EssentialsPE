<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseAPI;
use EssentialsPE\BaseFiles\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Lightning extends BaseCommand{
    /**
     * @param BaseAPI $api
     */
    public function __construct(BaseAPI $api){
        parent::__construct($api, "lightning", "Strike a lightning!", "[player [damage]]", "<player> [damage]", ["strike", "smite", "thor", "shock"]);
        $this->setPermission("essentials.lightning.use");
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
                $pos = $sender->getTargetBlock(100);
                $damage = 0;
                break;
            case 1:
            case 2:
                if(!($pos = $this->getAPI()->getPlayer($args[0]))){
                    $sender->sendMessage(TextFormat::RED . "[Error] Player not found.");
                    return false;
                }
                if(!isset($args[1])){
                    $args[1] = 0;
                }elseif(!is_numeric($args[1])){
                    $sender->sendMessage(TextFormat::RED . "[Error] Damage should be numeric");
                    return false;
                }
                $damage = $args[1];
                break;
            default:
                $this->sendUsage($sender, $alias);
                return false;
                break;
        }
        $this->getAPI()->strikeLightning($pos, $damage);
        $sender->sendMessage(TextFormat::YELLOW . "Lightning summoned!");
        return true;
    }
}