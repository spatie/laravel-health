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
    public function getSlackColor(): string
    {
        return match ($this) {
            self::ok() => '#2EB67D',
            self::warning() => '#ECB22E',
            self::failed(), self::crashed() => '#E01E5A',
            default => '',
        };
    }
}
