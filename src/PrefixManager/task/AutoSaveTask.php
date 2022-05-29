<?php
declare(strict_types=1);

namespace PrefixManager\task;

use pocketmine\scheduler\Task;
use PrefixManager\Loader;

class AutoSaveTask extends Task{

	public function onRun() : void{
		Loader::getInstance()->getPrefixFactory()->save();
		foreach(Loader::getInstance()->getPrefixManager()->getPrefixes() as $name => $prefix){
			Loader::getInstance()->getPrefixManager()->saveData($name, $prefix->jsonSerialize());
		}
	}
}