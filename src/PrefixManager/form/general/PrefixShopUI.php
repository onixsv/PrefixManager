<?php
declare(strict_types=1);

namespace PrefixManager\form\general;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use PrefixManager\form\Form;
use PrefixManager\Loader;
use PrefixManager\PrefixMarket;
use function json_encode;

class PrefixShopUI extends Form{

	public static function getFormId() : int{
		return 92471;
	}

	public static function sendToPlayer(Player $player) : void{
		$arr = [];
		foreach(Loader::getInstance()->getPrefixFactory()->getAllUIMarkets() as $market){
			$arr[] = ["text" => $market->getPrefix() . TextFormat::EOL . "구매가: " . TextFormat::GREEN . $market->getPrice()];
		}
		$encode = [
			"type" => "form",
			"title" => "칭호상점",
			"content" => "구매를 원하는 칭호를 클릭해주세요!",
			"buttons" => $arr
		];

		$pk = new ModalFormRequestPacket();
		$pk->formId = self::getFormId();
		$pk->formData = json_encode($encode);
		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public function handleResponse(Player $player, $data) : void{
		/** @var PrefixMarket[] $arr */
		$arr = [];
		foreach(Loader::getInstance()->getPrefixFactory()->getAllUIMarkets() as $market){
			$arr[] = $market;
		}

		if($data !== null){
			$selectedMarket = $arr[$data];

			$selectedMarket->buy($player);
		}
	}
}