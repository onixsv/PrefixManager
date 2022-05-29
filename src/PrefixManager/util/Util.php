<?php
declare(strict_types=1);

namespace PrefixManager\util;

use pocketmine\world\Position;
use function implode;

class Util{

	public static function positionHash(Position $pos) : string{
		return implode(":", [$pos->x, $pos->y, $pos->z, $pos->world->getFolderName()]);
	}
}