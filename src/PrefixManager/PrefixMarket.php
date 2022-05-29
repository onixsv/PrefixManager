<?php
declare(strict_types=1);

namespace PrefixManager;

use JsonSerializable;
use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

class PrefixMarket implements JsonSerializable{

	private int $price;

	private string $prefix;

	private ?Position $position;

	public function __construct(string $prefix, int $price, ?Position $pos = null){
		$this->prefix = $prefix;
		$this->price = $price;
		$this->position = $pos;
	}

	public function getPrefix() : string{
		return $this->prefix;
	}

	public function getPrice() : int{
		return $this->price;
	}

	public function buy(Player $player) : void{
		$prefix = Loader::getInstance()->getPrefixManager()->getPrefix($player);

		if($prefix->hasPrefix($this->getPrefix())){
			$player->sendMessage(Loader::$prefix . "이미 해당 칭호를 보유하고 있습니다.");
			return;
		}

		if(EconomyAPI::getInstance()->reduceMoney($player, $this->getPrice()) !== EconomyAPI::RET_SUCCESS){
			$player->sendMessage(Loader::$prefix . "구매에 필요한 돈이 부족합니다.");
			return;
		}
		$prefix->addPrefix($this->getPrefix());
		$player->sendMessage(Loader::$prefix . "성공적으로 " . $this->getPrefix() . TextFormat::RESET . TextFormat::GRAY . " 칭호를 구매하였습니다.");
	}

	public function jsonSerialize() : array{
		if($this->position !== null){
			return [
				"price" => $this->price,
				"prefix" => $this->prefix,
				"pos" => implode(":", [
					$this->position->x,
					$this->position->y,
					$this->position->z,
					$this->position->world->getFolderName()
				])
			];
		}else{
			return [
				"price" => $this->price,
				"prefix" => $this->prefix
			];
		}
	}

	public function isUIMarket() : bool{
		return $this->position === null;
	}

	public static function jsonDeserialize(array $data) : PrefixMarket{
		$pos = isset($data["pos"]) ? new Position((float) explode(":", $data["pos"])[0], (float) explode(":", $data["pos"])[1], (float) explode(":", $data["pos"])[2], Server::getInstance()->getWorldManager()->getWorldByName(explode(":", $data["pos"])[3])) : null;

		return new PrefixMarket((string) $data["prefix"], (int) $data["price"], $pos);
	}

	public function getPosition() : ?Position{
		return $this->position;
	}
}
