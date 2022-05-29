<?php
declare(strict_types=1);

namespace PrefixManager\form\general;

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
use function str_replace;
use function trim;

class FreePrefixUI extends Form{

	public static function getFormId() : int{
		return 13851;
	}

	public static function sendToPlayer(Player $player) : void{
		$pk = new ModalFormRequestPacket();
		$pk->formId = self::getFormId();
		$pk->formData = json_encode([
			"type" => "custom_form",
			"title" => "자유칭호 UI",
			"content" => [
				[
					"type" => "input",
					"text" => "사용하고 싶은 자유칭호를 입력해주세요. (최대 7글자까지 가능, [] 제거해도 됨.)"
				]
			]
		]);
		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_array($data)){
			if(trim($data[0] ?? "") !== ""){
				$ticket = ItemFactory::getInstance()->get(ItemIds::PAPER, 13, 1);
				if($player->getInventory()->contains($ticket)){
					$prefix = Loader::getInstance()->getPrefixManager()->getPrefix($player);
					if(!$prefix->hasPrefix($data[0])){
						$pre = str_replace(["[", "]"], ["", ""], $data[0]);
						$clean = TextFormat::clean($pre);
						if(mb_strlen($clean, "utf-8") < 8){
							$prefix->addPrefix("§6[§r " . $pre . "§6 ]");
							$player->getInventory()->removeItem($ticket);
							$player->sendMessage(Loader::$prefix . "자유칭호 티켓 1장을 소모하고 자유칭호를 추가했습니다.");
						}else{
							$player->sendMessage(Loader::$prefix . "자유칭호의 글자는 7글자 미만이어야 합니다.");
						}
					}else{
						$player->sendMessage(Loader::$prefix . "해당 칭호를 이미 갖고 있습니다.");
					}
				}else{
					$player->sendMessage(Loader::$prefix . "자유칭호 티켓이 부족합니다.");
				}
			}
		}
	}
}