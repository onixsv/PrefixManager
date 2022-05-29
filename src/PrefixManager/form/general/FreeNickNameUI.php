<?php
declare(strict_types=1);

namespace PrefixManager\form\general;

use OnixUtils\OnixUtils;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use PrefixManager\form\Form;
use PrefixManager\Loader;
use function is_array;
use function json_encode;
use function mb_strlen;
use function trim;

class FreeNickNameUI extends Form{

	public static function getFormId() : int{
		return 94271;
	}

	public static function sendToPlayer(Player $player) : void{
		$encode = [
			"type" => "custom_form",
			"title" => "§l닉네임 변경하기",
			"content" => [
				[
					"type" => "input",
					"text" => "사용하고 싶은 닉네임을 넣어주세요. (최대 5글자, 색 사용 불가, 사용 시 기존 닉네임은 사라짐)"
				]
			]
		];
		$pk = new ModalFormRequestPacket();
		$pk->formId = self::getFormId();
		$pk->formData = json_encode($encode);
		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_array($data)){
			$nick = $data[0] ?? "";
			if(trim($nick) !== ""){
				$need = ItemFactory::getInstance()->get(ItemIds::PAPER, 14, 1);
				if($player->getInventory()->contains($need)){
					$clean = TextFormat::clean($nick);
					if(mb_strlen($clean, "utf-8") <= 5){
						OnixUtils::message($player, "닉네임을 {$clean}§7(으)로 변경했습니다.");
						$prefix = Loader::getInstance()->getPrefixManager()->getPrefix($player);
						$prefix->setNickName($clean);
						$player->getInventory()->removeItem($need);
					}else{
						OnixUtils::message($player, "칭호의 글자는 최대 5글자 입니다.");
					}
				}else{
					OnixUtils::message($player, "닉네임 변경권이 부족합니다.");
				}
			}else{
				OnixUtils::message($player, "닉네임을 입력해주세요.");
			}
		}
	}
}