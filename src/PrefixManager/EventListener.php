<?php
declare(strict_types=1);

namespace PrefixManager;

use pocketmine\block\utils\SignText;
use pocketmine\block\WallSign;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use PrefixManager\form\Form;

class EventListener implements Listener{

	protected ?array $work;

	private static ?EventListener $instance = null;

	public function __construct(){
		self::$instance = $this;
	}

	public static function getInstance() : EventListener{
		return self::$instance;
	}

	public function addWork(Player $player, string $prefix, int $price){
		$this->work[$player->getName()] = [
			"prefix" => $prefix,
			"price" => $price
		];
	}

	public function getWork(Player $player) : ?array{
		return $this->work[$player->getName()] ?? null;
	}

	public function handlePacketReceive(DataPacketReceiveEvent $event){
		$packet = $event->getPacket();
		$player = $event->getOrigin()->getPlayer();
		if($packet instanceof ModalFormResponsePacket){
			if(($form = Form::getFormById($packet->formId)) instanceof Form){
				$form->handleResponse($player, json_decode($packet->formData, true));
			}
		}
	}

	public function handlePlayerChat(PlayerChatEvent $event){
		$player = $event->getPlayer();

		$prefix = Loader::getInstance()->getPrefixManager()->getPrefix($player);

		$message = $player->hasPermission(DefaultPermissions::ROOT_OPERATOR) ? $event->getMessage() : TextFormat::clean($event->getMessage());

		$event->setMessage($message);

		if($player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
			$format = $prefix->getNowPrefix() . "§r§f " . TextFormat::RESET . $prefix->getNickName() . " §d> " . $event->getMessage();
		}else{
			$format = $prefix->getNowPrefix() . "§r§7 " . TextFormat::RESET . $prefix->getNickName() . " §r§7> " . $event->getMessage();
		}
		$event->setFormat($format);
	}

	public function handlePlayerJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();

		Loader::getInstance()->getPrefixManager()->setUp($player);

		Loader::getInstance()->getPrefixManager()->getPrefix($player)->save();
	}

	public function handlePlayerQuit(PlayerQuitEvent $event){
		$player = $event->getPlayer();
		Loader::getInstance()->getPrefixManager()->getPrefix($player)->save();
		Loader::getInstance()->getPrefixManager()->remove($player);
	}

	/**
	 * @param PlayerInteractEvent $event
	 *
	 * @handleCancelled true
	 */
	public function handlePlayerInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();

		if($this->getWork($player) !== null){
			$work = $this->getWork($player);

			$tile = $block->getPosition()->getWorld()->getBlock($block->getPosition());

			if($tile instanceof WallSign){
				Loader::getInstance()->getPrefixFactory()->addMarket(new PrefixMarket((string) $work["prefix"], (int) $work["price"], $block->getPosition()));
				$tile->updateText($player, new SignText([
					"§b§l[ 칭호상점 ]",
					"§b§l칭호: §r" . $work["prefix"],
					"§b§l가격: §r§f" . $work["price"],
					"§b§l터치시 구매됩니다."
				]));
				unset($this->work[$player->getName()]);
				$player->sendMessage(Loader::$prefix . "추가되었습니다.");
			}else{
				$player->sendMessage(Loader::$prefix . "표지판을 터치해주세요.");
			}
			return;
		}
		if(($market = Loader::getInstance()->getPrefixFactory()->getMarketByPosition($block->getPosition())) instanceof PrefixMarket){
			$market->buy($player);
		}
	}

	/**
	 * @param BlockBreakEvent $event
	 *
	 * @handleCancelled true
	 */
	public function handleBlockBreak(BlockBreakEvent $event){
		$block = $event->getBlock();
		$player = $event->getPlayer();
		if(($market = Loader::getInstance()->getPrefixFactory()->getMarketByPosition($block->getPosition())) instanceof PrefixMarket){
			if($player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
				Loader::getInstance()->getPrefixFactory()->removeMarket($market);
				$player->sendMessage(Loader::$prefix . "칭호상점을 제거했습니다.");
			}else{
				$player->sendMessage(Loader::$prefix . "칭호상점을 부술 수 없습니다.");
				$event->cancel();
			}
		}
	}
}
