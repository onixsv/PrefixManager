<?php
declare(strict_types=1);

namespace PrefixManager;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use PrefixManager\form\Form;
use PrefixManager\form\general\MainUI;
use PrefixManager\form\manage\CreateShopUI;
use PrefixManager\form\manage\ManageUI;
use PrefixManager\task\AutoSaveTask;

class Loader extends PluginBase{
	use SingletonTrait;

	public static string $prefix = "§b§l[알림] §r§7";

	/** @var PrefixManager */
	protected PrefixManager $prefixManager;

	/** @var PrefixFactory */
	protected PrefixFactory $prefixFactory;

	/** @var Config */
	protected Config $config;

	protected function onLoad() : void{
		self::setInstance($this);
	}

	protected function onEnable() : void{
		$this->prefixManager = new PrefixManager($this->getDataFolder());
		$this->prefixFactory = new PrefixFactory($this);
		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
		Form::init();
		$this->saveResource('config.yml');
		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		if((bool) $this->getConfig()->getNested("do-autosave", true)){
			$this->getScheduler()->scheduleRepeatingTask(new AutoSaveTask(), 1200 * 5);
		}
	}

	public function getConfig() : Config{
		return $this->config;
	}

	protected function onDisable() : void{
		$this->save();
	}

	public function save() : void{
		$this->prefixFactory->save();
		foreach($this->prefixManager->getPrefixes() as $prefix){
			$this->prefixManager->saveData($this->getServer()->getOfflinePlayer($prefix->getName()), $prefix->jsonSerialize());
		}
	}

	public function getPrefixManager() : PrefixManager{
		return $this->prefixManager;
	}

	public function getPrefixFactory() : PrefixFactory{
		return $this->prefixFactory;
	}

	public static function getInstance() : Loader{
		return self::$instance;
	}

	public function getDefaultPrefix() : string{
		return $this->getConfig()->get("default-prefix");
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(!$sender instanceof Player)
			return true;
		if($command->getName() === "칭호"){
			MainUI::sendToPlayer($sender);
			return true;
		}
		if($command->getName() === "칭호관리"){
			if(!$sender->hasPermission("prefix.manage")){
				return false;
			}
			ManageUI::sendToPlayer($sender);
			return true;
		}
		if($command->getName() === "칭호상점"){
			if(!$sender->hasPermission("prefix.shop")){
				return false;
			}
			CreateShopUI::sendToPlayer($sender);
		}
		return true;
	}
}
