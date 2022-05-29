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

class PrefixDetailUI extends Form{

	public static function getFormId() : int{
		return 4861;
	}

	public static function sendToPlayer(Player $player) : void{
		$prefix = Loader::getInstance()->getPrefixManager()->getPrefix(Server::getInstance()->getOfflinePlayer(PrefixQueue::$manageQueue[$player->getName()]));
		$encode = [
			"type" => "form",
			"title" => $prefix->getName() . " 님 칭호 관리!",
			"content" => "관리를 원하시는 항목을 선택해주세요!",
			"buttons" => [
				[
					"text" => "나가기"
				],
				[
					"text" => "칭호 목록 보기"
				],
				[
					"text" => "칭호 지급하기"
				],
				[
					"text" => "칭호 제거하기"
				],
				[
					"text" => "한닉 설정하기"
				]
			]
		];

		$pk = new ModalFormRequestPacket();
		$pk->formId = self::getFormId();
		$pk->formData = json_encode($encode);
		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public function handleResponse(Player $player, $data) : void{
		switch($data){
			case 0:
				break;
			case 1:
				PrefixListUI::sendToPlayer($player);
				break;
			case 2:
				PrefixAddUI::sendToPlayer($player);
				break;
			case 3:
				$player->sendMessage(Loader::$prefix . "준비중인 기능입니다.");
				break;
			case 4:
				NickNameUI::sendToPlayer($player);
				break;
		}
	}
}