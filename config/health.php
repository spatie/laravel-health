<?php

use Spatie\Health\ResultStores\EloquentHealthResultStore;
use Spatie\Health\ResultStores\JsonFileHeathResultStore;

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
    ]
];
