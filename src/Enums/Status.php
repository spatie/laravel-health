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
        return match ($this->value) {
            self::ok()->value => '#2EB67D',
            self::warning()->value => '#ECB22E',
            self::failed()->value, self::crashed()->value => '#E01E5A',
            default => '',
        };
    }
}


