<?php
declare(strict_types=1);

namespace PrefixManager\form\manage;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use PrefixManager\EventListener;
use PrefixManager\form\Form;
use PrefixManager\Loader;
use PrefixManager\PrefixMarket;
use function is_numeric;
use function json_encode;
use function substr;
use function trim;

class CreateShopUI extends Form{

	public static function getFormId() : int{
		return 83261;
	}

	public static function sendToPlayer(Player $player) : void{
		$encode = [
			"type" => "custom_form",
			"title" => "칭호상점",
			"content" => [
				[
					"type" => "input",
					"text" => "판매할 칭호를 입력해주세요."
				],
				[
					"type" => "input",
					"text" => "가격을 입력해주세요."
				],
				[
					"type" => "toggle",
					"text" => "UI 상점이면 활성화, 표지판 상점이면 비활성화를 해주세요.",
					"default" => true
				]
			]
		];

		$pk = new ModalFormRequestPacket();
		$pk->formId = self::getFormId();
		$pk->formData = json_encode($encode);
		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public function handleResponse(Player $player, $data) : void{
		if(trim($data[0] ?? "") === ""){
			$player->sendMessage(Loader::$prefix . "판매할 칭호를 입력해주세요.");
			return;
		}
		$clean = TextFormat::clean($data[0] ?? "");
		if(substr($clean, 0, 1) !== "["){
			$player->sendMessage(Loader::$prefix . "칭호의 시작은 [ 이어야 합니다.");
			return;
		}
		if(substr($clean, -1) !== "]"){
			$player->sendMessage(Loader::$prefix . "칭호의 끝은 ] 이어야 합니다.");
			return;
		}

		if(!isset($data[1]) or !is_numeric($data[1])){
			$player->sendMessage(Loader::$prefix . "가격을 입력해주세요.");
			return;
		}

		$bool = (bool) $data[2];
		if($bool){
			$market = new PrefixMarket((string) $data[0], (int) $data[1], null);
			Loader::getInstance()->getPrefixFactory()->addMarket($market);
			$player->sendMessage(Loader::$prefix . "추가되었습니다.");
		}else{
			EventListener::getInstance()->addWork($player, (string) $data[0], (int) $data[1]);
			$player->sendMessage(Loader::$prefix . "추가를 원하는 표지판을 클릭해주세요.");
		}
	}
}