<?php
declare(strict_types=1);

namespace PrefixManager\form;

use pocketmine\player\Player;
use PrefixManager\form\general\FreeNickNameUI;
use PrefixManager\form\general\FreePrefixUI;
use PrefixManager\form\general\MainUI;
use PrefixManager\form\general\PrefixSelectUI;
use PrefixManager\form\general\PrefixShopUI;
use PrefixManager\form\manage\CreateShopUI;
use PrefixManager\form\manage\ManageUI;
use PrefixManager\form\manage\NickNameUI;
use PrefixManager\form\manage\PrefixAddUI;
use PrefixManager\form\manage\PrefixDetailUI;
use PrefixManager\form\manage\PrefixListUI;

abstract class Form{

	/** @var Form[] */
	private static array $formList = [];

	final public static function init(){
		self::$formList[MainUI::getFormId()] = new MainUI();
		self::$formList[PrefixSelectUI::getFormId()] = new PrefixSelectUI();
		self::$formList[CreateShopUI::getFormId()] = new CreateShopUI();
		self::$formList[NickNameUI::getFormId()] = new NickNameUI();
		self::$formList[ManageUI::getFormId()] = new ManageUI();
		self::$formList[PrefixAddUI::getFormId()] = new PrefixAddUI();
		self::$formList[PrefixDetailUI::getFormId()] = new PrefixDetailUI();
		self::$formList[PrefixListUI::getFormId()] = new PrefixListUI();
		self::$formList[PrefixShopUI::getFormId()] = new PrefixShopUI();
		self::$formList[FreePrefixUI::getFormId()] = new FreePrefixUI();
		self::$formList[FreeNickNameUI::getFormId()] = new FreeNickNameUI();
	}

	final public static function getFormById(int $id) : ?Form{
		return self::$formList[$id] ?? null;
	}

	abstract public static function sendToPlayer(Player $player) : void;

	abstract public static function getFormId() : int;

	abstract public function handleResponse(Player $player, $data) : void;
}
