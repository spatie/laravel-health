<?php

namespace Spatie\Health\Exceptions;

use Exception;
use Spatie\Health\Models\HealthCheckResultHistoryItem;
use Spatie\Health\ResultStores\ResultStore;

class CouldNotSaveResultsInStore extends Exception
{
    public static function make(ResultStore $store, Exception $exception): self
    {
        $storeClass = $store::class;

        return new self(
            message: "Could not save results in the `{$storeClass}` did not complete. An exception was thrown with this message: `".get_class($exception).": {$exception->getMessage()}`",
            previous: $exception,
        );
    }

    public static function doesNotExtendHealthCheckResultHistoryItem(mixed $invalidValue): self
    {
        $className = HealthCheckResultHistoryItem::class;

        return new self(
            "You tried to register an invalid HealthCheckResultHistoryItem model: `{$invalidValue}`. A valid model should extend `{$className}`"
        );
    }
}
