<?php
declare(strict_types=1);

namespace PrefixManager\form\general;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\player\Player;
use PrefixManager\form\Form;
use function json_encode;

class MainUI extends Form{

	public static function getFormId() : int{
		return 13513;
	}

	public static function sendToPlayer(Player $player) : void{
		$encode = [
			"type" => "form",
			"title" => "칭호 UI",
			"content" => "원하시는 항목을 선택해주세요!",
			"buttons" => [
				[
					"text" => "§l나가기\n현재 메뉴에서 나갑니다."
				],
				[
					"text" => "§l칭호 설정하기\n내 칭호를 설정합니다."
				],
				[
					"text" => "§l칭호 상점\n칭호 상점에 입장합니다."
				],
				[
					"text" => "§l자유 칭호\n자유칭호를 설정합니다."
				],
				[
					"text" => "§l닉네임 설정\n닉네임을 설정합니다."
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
				PrefixSelectUI::sendToPlayer($player);
				break;
			case 2:
				PrefixShopUI::sendToPlayer($player);
				break;
			case 3:
				FreePrefixUI::sendToPlayer($player);
				break;
			case 4:
				FreeNickNameUI::sendToPlayer($player);
				break;
		}
	}
}