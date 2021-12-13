<?php

namespace Spatie\Health\Exceptions;

use Exception;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\Models\HealthCheckResultHistoryItem;

class CouldNotSaveResultsInStore extends Exception
{
    public static function make(ResultStore $store, Exception $exception): self
    {
        $storeClass = get_class($store);

        return new self(
            message: "Could not save results in the `{$storeClass}` did not complete. An exception was thrown with this message: `{$exception->getMessage()}`",
            previous: $exception,
        );
    }
    
    public static function doesNotExtendHealthCheckResultHistoryItem(mixed $invalidValue): self
    {
        return new self(
            "You tried to register an invalid HealthCheckResultHistoryItem model: `{$invalidValue}`. A valid model should extend " . HealthCheckResultHistoryItem::class
        );
    }
}
