<?php
declare(strict_types=1);

namespace PrefixManager;

use pocketmine\world\Position;
use PrefixManager\util\Util;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function json_decode;

class PrefixFactory{

	protected Loader $plugin;

	/** @var PrefixMarket[] */
	protected array $uiPrefixMarket = [];

	/** @var PrefixMarket[] */
	protected array $signPrefixMarket = [];

	public function __construct(Loader $plugin){
		$this->plugin = $plugin;
		$this->load();
	}

	public function load(){
		$data = (file_exists($this->plugin->getDataFolder() . "PrefixData.json")) ? json_decode(file_get_contents($this->plugin->getDataFolder() . "PrefixData.json"), true) : [];

		foreach($data as $datum){
			$market = PrefixMarket::jsonDeserialize($datum);

			if($market->isUIMarket()){
				$this->uiPrefixMarket[$market->getPrefix()] = $market;
			}else{
				$this->signPrefixMarket[Util::positionHash($market->getPosition())] = $market;
			}
		}
	}

	public function getMarketByPosition(Position $pos) : ?PrefixMarket{
		return $this->signPrefixMarket[Util::positionHash($pos)] ?? null;
	}

	public function getMarketByPrefix(string $prefix) : ?PrefixMarket{
		return $this->uiPrefixMarket[$prefix] ?? null;
	}

	public function addMarket(PrefixMarket $market){
		if($market->isUIMarket()){
			$this->uiPrefixMarket[$market->getPrefix()] = $market;
		}else{
			$this->signPrefixMarket[Util::positionHash($market->getPosition())] = $market;
		}
	}

	public function removeMarket(PrefixMarket $market){
		if($market->isUIMarket()){
			unset($this->uiPrefixMarket[$market->getPrefix()]);
		}else{
			unset($this->signPrefixMarket[Util::positionHash($market->getPosition())]);
		}
	}

	/**
	 * @return PrefixMarket[]
	 */
	public function getAllUIMarkets() : array{
		$arr = [];
		foreach($this->uiPrefixMarket as $str => $market){
			$arr[] = $market;
		}
		return $arr;
	}

	/**
	 * @return PrefixMarket[]
	 */
	public function getAllSignMarkets() : array{
		$arr = [];
		foreach($this->signPrefixMarket as $str => $market){
			$arr[] = $market;
		}
		return $arr;
	}

	public function save(){
		$arr = [];
		foreach($this->uiPrefixMarket as $string => $markets){
			$arr[] = $markets->jsonSerialize();
		}
		foreach($this->signPrefixMarket as $str => $market){
			$arr[] = $market->jsonSerialize();
		}

		file_put_contents($this->plugin->getDataFolder() . "PrefixData.json", json_encode($arr, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING));
	}
}
