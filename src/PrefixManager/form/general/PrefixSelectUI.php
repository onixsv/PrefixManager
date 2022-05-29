<?php
declare(strict_types=1);

namespace PrefixManager\form\general;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use PrefixManager\form\Form;
use PrefixManager\Loader;
use function json_encode;

class PrefixSelectUI extends Form{

	public static function getFormId() : int{
		return 32851;
	}

	public static function sendToPlayer(Player $player) : void{
		$arr = [];

		$c = 0;

		$prefix = Loader::getInstance()->getPrefixManager()->getPrefix($player);

		foreach($prefix->getAll() as $prefixs){
			if($prefix->getNowPrefix() === $prefixs){
				$arr[] = ["text" => $c . ". " . $prefixs . TextFormat::EOL . TextFormat::GREEN . "[ 착용중 ]"];
			}else{
				$arr[] = ["text" => $c . ". " . $prefixs];
			}
			$c++;
		}

		$encode = [
			"type" => "form",
			"title" => "칭호 선택 UI",
			"content" => "선택하길 원하는 칭호를 선택해주세요",
			"buttons" => $arr
		];

		$pk = new ModalFormRequestPacket();
		$pk->formId = self::getFormId();
		$pk->formData = json_encode($encode);
		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public function handleResponse(Player $player, $data) : void{
		if($data === null){
			return;
		}

		$prefix = Loader::getInstance()->getPrefixManager()->getPrefix($player);

		$prefix->selectPrefix($data);

		$player->sendMessage(Loader::$prefix . "칭호를 " . $prefix->getNowPrefix() . TextFormat::RESET . TextFormat::WHITE . " 으(로) 변경했습니다.");
	}
}