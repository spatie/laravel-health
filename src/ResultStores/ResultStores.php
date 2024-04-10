<?php

namespace Spatie\Health\ResultStores;

use Illuminate\Support\Collection;

class ResultStores
{
    /** @return Collection<int, ResultStore> */
    public static function createFromConfig(): Collection
    {
        $defaultStores = explode(',', config('health.result_stores.default'));
        $storesConfig = config('health.result_stores.stores');
        $resultStores = new Collection();

        foreach ($defaultStores as $storeKey) {
            $storeKey = trim($storeKey); // Trim any whitespace
            $selectedStoreConfig = $storesConfig[$storeKey] ?? null;
            if ($selectedStoreConfig) {
                $resultStoreClass = $selectedStoreConfig['class'];
                $resultStores->push(app($resultStoreClass, $selectedStoreConfig));
            }
        }

        return $resultStores;
    }
}
