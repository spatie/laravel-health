<?php

namespace Spatie\Health\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self ok()
 * @method static self warning()
 * @method static self failed()
 * @method static self crashed()
 * @method static self skipped()
 */
class Status extends Enum
{
}
