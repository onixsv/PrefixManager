<?php
declare(strict_types=1);

namespace PrefixManager;

use pocketmine\player\IPlayer;
use pocketmine\player\Player;
use PrefixManager\prefix\Prefix;
use function file_put_contents;
use function is_dir;
use function json_decode;
use function json_encode;
use function mkdir;
use function strtolower;
use function substr;

class PrefixManager{

	/** @var Prefix[] */
	protected array $prefix = [];

	protected string $dir;

	public function __construct(string $dir){
		$this->dir = $dir;
	}

	public function getPrefix(IPlayer $player) : Prefix{
		if(isset($this->prefix[$player->getName()])){
			return $this->prefix[$player->getName()];
		}
		$dir = $this->dir . substr(strtolower($player->getName()), 0, 1) . DIRECTORY_SEPARATOR;

		if(!is_dir($dir)){
			@mkdir($dir, 0777);
		}

		$file = $dir . strtolower($player->getName()) . ".json";
		if(!file_exists($file)){
			file_put_contents($file, json_encode([
				"prefix" => [
					0 => Loader::getInstance()->getDefaultPrefix()
				],
				"name" => strtolower($player->getName()),
				"nickName" => $player->getName(),
				"selectedPrefix" => 0
			]/*, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING*/)); // Disable formatting json because it is too dirty
		}

		$data = json_decode(file_get_contents($file), true);

		$prefix = new Prefix($player, $data["prefix"], $data["nickName"], (int) $data["selectedPrefix"]);

		return $prefix;
	}

	public function setUp(Player $player){
		$this->prefix[$player->getName()] = $this->getPrefix($player);
	}

	public function isExistsData($player) : bool{
		$player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);

		$dir = $this->dir . substr($player, 0, 1) . DIRECTORY_SEPARATOR;
		if(!is_dir($dir)){
			return false;
		}
		$file = $dir . $player . ".json";
		if(!file_exists($file)){
			return false;
		}
		return true;
	}

	/**
	 * @return Prefix[]
	 */
	public function getPrefixes() : array{
		return $this->prefix;
	}

	public function saveData($player, array $data){
		$player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);

		$dir = $this->dir . substr($player, 0, 1) . DIRECTORY_SEPARATOR;
		if(!is_dir($dir)){
			@mkdir($dir, 0777);
		}
		$file = $dir . $player . ".json";
		file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING));
	}

	public function remove(Player $player){
		if(isset($this->prefix[$player->getName()]))
			unset($this->prefix[$player->getName()]);
	}
}
