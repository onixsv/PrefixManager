<?php
declare(strict_types=1);

namespace PrefixManager\form\manage;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use PrefixManager\form\Form;
use PrefixManager\Loader;
use PrefixManager\PrefixQueue;
use function json_encode;
use function trim;

class ManageUI extends Form{

	public static function getFormId() : int{
		return 37161;
	}

	public static function sendToPlayer(Player $player) : void{
		$encode = [
			"type" => "custom_form",
			"title" => "칭호 | 관리 UI",
			"content" => [
				[
					"type" => "input",
					"text" => "관리할 플레이어의 닉네임을 적어주세요."
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
			$player->sendMessage(Loader::$prefix . "관리할 플레이어의 닉네임을 적어주세요.");
			return;
		}
		if(!Loader::getInstance()->getPrefixManager()->isExistsData($data[0])){
			$player->sendMessage(Loader::$prefix . "해당 플레이어의 데이터 파일이 존재하지 않습니다.");
			return;
		}

		if(($target = Server::getInstance()->getPlayerExact($data[0])) instanceof Player){
			$target = Server::getInstance()->getPlayerExact($data[0]);
		}else{
			$target = Server::getInstance()->getOfflinePlayer($data[0]);
		}

		PrefixQueue::$manageQueue[$player->getName()] = $target->getName();

		PrefixDetailUI::sendToPlayer($player);
	}
}