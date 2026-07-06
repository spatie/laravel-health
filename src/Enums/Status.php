<?php

namespace Spatie\Health\Enums;

enum Status: string
{
    case Ok = 'ok';

    case Warning = 'warning';

    case Failed = 'failed';

    case Crashed = 'crashed';

    case Skipped = 'skipped';

    public function getSlackColor(): string
    {
        return match ($this) {
            self::Ok => '#2EB67D',
            self::Warning => '#ECB22E',
            self::Failed, self::Crashed => '#E01E5A',
        };
    }

    public function getCliColor(): string
    {
        return match ($this) {
            self::Ok => 'text-green-600',
            self::Warning => 'text-yellow-600',
            self::Skipped => 'text-blue-600',
            self::Failed, self::Crashed => 'text-red-600',
        };
    }

    public function getBackgroundColor(): string
    {
        return match ($this) {
            self::Ok => 'md:bg-emerald-100 md:dark:bg-emerald-800',
            self::Warning => 'md:bg-yellow-100 md:dark:bg-yellow-800',
            self::Skipped => 'md:bg-blue-100 md:dark:bg-blue-800',
            self::Failed, self::Crashed => 'md:bg-red-100 md:dark:bg-red-800',
        };
    }

    public function getIconColor(): string
    {
        return match ($this) {
            self::Ok => 'text-emerald-500',
            self::Warning => 'text-yellow-500',
            self::Skipped => 'text-blue-500',
            self::Failed, self::Crashed => 'text-red-500',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Ok => 'check-circle',
            self::Warning => 'exclamation-circle',
            self::Skipped => 'arrow-circle-right',
            self::Failed, self::Crashed => 'x-circle',
        };
    }
}
