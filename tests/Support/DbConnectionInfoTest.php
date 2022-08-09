<?php

use Illuminate\Database\ConnectionResolverInterface;
use Spatie\Health\Support\DbConnectionInfo;

it('can determine the table size in mb', function() {
   $connection = app(ConnectionResolverInterface::class)->connection('mysql');

   $connectionInfo = new DbConnectionInfo();

   $size = $connectionInfo->tableSizeInMb($connection, 'health_check_result_history_items');

   expect($size)->toBeGreaterThan(0);
});
