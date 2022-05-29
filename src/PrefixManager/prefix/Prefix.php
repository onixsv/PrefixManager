<?php
declare(strict_types=1);

namespace PrefixManager\prefix;

use JsonSerializable;
use pocketmine\player\IPlayer;
use pocketmine\utils\TextFormat;
use PrefixManager\Loader;
use function array_search;
use function strtolower;

class Prefix implements JsonSerializable{

	/** @var string[] */
	protected array $prefixes = [];

	protected string $name;

	protected string $nickName;

	protected int $selectedPrefix = 0;

	/**
	 * Prefix constructor.
	 *
	 * @param IPlayer $player
	 * @param array   $prefixes
	 * @param string  $nickName
	 * @param int     $selectedPrefix
	 */
	public function __construct(IPlayer $player, array $prefixes, string $nickName, int $selectedPrefix){
		$this->prefixes = $prefixes;
		$this->name = strtolower($player->getName());
		$this->nickName = $nickName;
		$this->selectedPrefix = $selectedPrefix;
	}

	public function getPrefix(int $index) : ?string{
		return $this->prefixes[$index] ?? null;
	}

	public function getKeyByPrefix(string $prefix) : ?int{
		$key = array_search($prefix, $this->prefixes);
		if($key !== false){
			return $key;
		}
		return null;
	}

	public function getAll() : array{
		return $this->prefixes;
	}

	public function addPrefix(string $prefix){
		$this->prefixes[] = $prefix;
	}

	public function removePrefix(int $index){
		unset($this->prefixes[$index]);
	}

	public function jsonSerialize() : array{
		return [
			"name" => $this->name,
			"prefix" => $this->prefixes,
			"nickName" => $this->nickName,
			"selectedPrefix" => $this->selectedPrefix
		];
	}

	public function getNowPrefix() : string{
		return $this->getPrefix($this->selectedPrefix);
	}

	public function selectPrefix(int $index){
		$this->selectedPrefix = $index;
	}

	public static function jsonDeserialize(IPlayer $player, array $data) : Prefix{
		return new Prefix($player, $data["prefix"], (string) $data["nickName"], (int) $data["selectedPrefix"]);
	}

	public function getName() : string{
		return $this->name;
	}

	public function getNickName() : string{
		return $this->nickName;
	}

	public function setNickName(string $nickName){
		$this->nickName = $nickName;
	}

	public function save(){
		Loader::getInstance()->getPrefixManager()->saveData($this->getName(), $this->jsonSerialize());
	}

	public function hasPrefix(string $prefix) : bool{
		$prefix = TextFormat::clean($prefix);

		foreach($this->prefixes as $hasPrefix){
			if(TextFormat::clean($hasPrefix) === $prefix){
				return true;
			}
		}
		return false;
	}
}
