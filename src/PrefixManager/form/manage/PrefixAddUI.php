<?php
declare(strict_types=1);

namespace PrefixManager\form\manage;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use PrefixManager\form\Form;
use PrefixManager\Loader;
use PrefixManager\PrefixQueue;
use function json_encode;
use function substr;
use function trim;

class PrefixAddUI extends Form{

	public static function getFormId() : int{
		return 83621;
	}

	public static function sendToPlayer(Player $player) : void{
		$data = Loader::getInstance()->getPrefixManager()->getPrefix(Server::getInstance()->getOfflinePlayer(PrefixQueue::$manageQueue[$player->getName()]));

		$encode = [
			"type" => "custom_form",
			"title" => $data->getName() . "님 칭호 추가!",
			"content" => [
				[
					"type" => "input",
					"text" => "칭호를 입력해주세요. 칭호의 앞과 끝에는 [ ] 가 붙어있어야 합니다."
				]
			]
		];

		$pk = new ModalFormRequestPacket();
		$pk->formId = self::getFormId();
		$pk->formData = json_encode($encode);
		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public function handleResponse(Player $player, $data) : void{
		try{
			$prefix = Loader::getInstance()->getPrefixManager()->getPrefix(Server::getInstance()->getOfflinePlayer(PrefixQueue::$manageQueue[$player->getName()]));
			if(trim($data[0] ?? "") === ""){
				$player->sendMessage(Loader::$prefix . "칭호를 입력해주세요.");
				return;
			}
			$clean = TextFormat::clean($data[0]);

			if(substr($clean, 0, 1) !== "["){
				$player->sendMessage(Loader::$prefix . "칭호의 시작은 [ 이어야 합니다.");
				return;
			}
			if(substr($clean, -1) !== "]"){
				$player->sendMessage(Loader::$prefix . "칭호의 끝은 ] 이어야 합니다.");
				return;
			}
			$prefix->addPrefix($data[0]);
			$player->sendMessage(Loader::$prefix . $prefix->getName() . "님에게 " . $data[0] . TextFormat::RESET . TextFormat::GRAY . " 칭호를 지급하였습니다.");
		}finally{
			unset(PrefixQueue::$manageQueue[$player->getName()]);
		}
	}
}