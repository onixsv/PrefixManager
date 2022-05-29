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
use function trim;

class NickNameUI extends Form{

	public static function getFormId() : int{
		return 65442;
	}

	public static function sendToPlayer(Player $player) : void{
		$data = Loader::getInstance()->getPrefixManager()->getPrefix(Server::getInstance()->getOfflinePlayer(PrefixQueue::$manageQueue[$player->getName()]));

		$encode = [
			"type" => "custom_form",
			"title" => $data->getName() . "님 닉네임 설정!",
			"content" => [
				[
					"type" => "input",
					"text" => "닉네임을 입력해주세요."
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
				$player->sendMessage(Loader::$prefix . "한닉을 입력해주세요.");
				return;
			}
			$prefix->setNickName($data[0]);
			$player->sendMessage(Loader::$prefix . $prefix->getName() . "님의 닉네임을 " . $data[0] . TextFormat::RESET . TextFormat::GRAY . "(으)로 설정하였습니다.");
		}finally{
			unset(PrefixQueue::$manageQueue[$player->getName()]);
		}
	}
}