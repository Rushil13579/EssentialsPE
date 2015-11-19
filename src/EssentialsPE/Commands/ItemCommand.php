<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseAPI;
use EssentialsPE\BaseFiles\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ItemCommand extends BaseCommand{
    /**
     * @param BaseAPI $api
     */
    public function __construct(BaseAPI $api){
        parent::__construct($api, "item", "Gives yourself an item", "<item[:damage]> [amount]", false, ["i"]);
        $this->setPermission("essentials.item");
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
        if(!$sender instanceof Player){
            $this->sendUsage($sender, $alias);
            return false;
        }
        if(($gm = $sender->getGamemode()) === Player::CREATIVE || $gm === Player::SPECTATOR){
            $sender->sendMessage(TextFormat::RED . "[Error] You're in " . $this->getAPI()->getServer()->getGamemodeString($gm) . " mode");
            return false;
        }
        if(count($args) < 1 || count($args) > 2){
            $this->sendUsage($sender, $alias);
            return false;
        }

        //Getting the item...
        $item_name = array_shift($args);
        $item = $this->getAPI()->getItem($item_name);

        if($item->getID() === 0){
            $sender->sendMessage(TextFormat::RED . "Unknown item \"" . $item_name . "\"");
            return false;
        }elseif(!$sender->hasPermission("essentials.itemspawn.item-all") && !$sender->hasPermission("essentials.itemspawn.item-" . $item->getName() && !$sender->hasPermission("essentials.itemspawn.item-" . $item->getID()))){
            $sender->sendMessage(TextFormat::RED . "You can't spawn this item");
            return false;
        }

        //Setting the amount...
        $amount = array_shift($args);
        if(!isset($amount) || !is_numeric($amount)){
            if(!$sender->hasPermission("essentials.oversizedstacks")){
                $item->setCount($item->getMaxStackSize());
            }else{
                $item->setCount($this->getPlugin()->getConfig()->get("oversized-stacks"));
            }
        }else{
            $item->setCount($amount);
        }

        //Getting other values... TODO
        /*foreach($args as $a){
            //Example
            if(stripos(strtolower($a), "color") !== false){
                $v = explode(":", $a);
                $color = $v[1];
            }
        }*/

        //Giving the item...
        $sender->getInventory()->setItem($sender->getInventory()->firstEmpty(), $item);
        $sender->sendMessage(TextFormat::YELLOW . "Giving " . TextFormat::RED . $item->getCount() . TextFormat::YELLOW . " of " . TextFormat::RED . ($item->getName() === "Unknown" ? $item_name : $item->getName()));
        return false;
    }
}
