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

class PrefixListUI extends Form{

	public static function getFormId() : int{
		return 38261;
	}

	public static function sendToPlayer(Player $player) : void{
		$arr = [];
		$data = Loader::getInstance()->getPrefixManager()->getPrefix(Server::getInstance()->getOfflinePlayer(PrefixQueue::$manageQueue[$player->getName()]));

		$c = 0;
		foreach($data->getAll() as $prefix){
			$arr[] = ["text" => $c . ". " . $prefix];
		}

		$encode = [
			"type" => "form",
			"title" => $data->getName() . "님 칭호 관리!",
			"content" => $data->getName() . "님의 칭호 목록입니다." . TextFormat::EOL . TextFormat::EOL,
			"buttons" => $arr
		];

		$pk = new ModalFormRequestPacket();
		$pk->formId = self::getFormId();
		$pk->formData = json_encode($encode);
		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public function handleResponse(Player $player, $data) : void{
		try{
			if($data === null){
				return;
			}
		}finally{
			if(isset(PrefixQueue::$manageQueue[$player->getName()]))
				unset(PrefixQueue::$manageQueue[$player->getName()]);
		}
	}
}