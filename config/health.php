<?php

use Spatie\Health\ResultStores\EloquentHealthResultStore;
use Spatie\Health\ResultStores\JsonFileHealthResultStore;

return [

    /*
     * A result store is responsible for saving the results of the checks. The
     * `EloquentHealthResultStore` will save results in the database. You
     * can use multiple stores at the same time.
     */
    'result_stores' => [
        EloquentHealthResultStore::class,

        /*
        JsonFileHeathResultStore::class => [
            'disk' => 's3',
            'file_name' => 'health.json',
        ],

        */
    ],


    /*
     * The amount of days the `EloquentHealthResultStore` will keep history
     * before pruning items.
     */
    'keep_history_for_days' => 100,
];
