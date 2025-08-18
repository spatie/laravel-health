<?php

namespace Spatie\Health\Checks\Checks;

use Exception;
use Illuminate\Support\Facades\DB;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Traits\HasDatabaseConnection;

class DatabaseCheck extends Check
{
    use HasDatabaseConnection;

    public function run(): Result
    {
        $connectionName = $this->connectionName ?? $this->getDefaultConnectionName();

        $result = Result::make()->meta([
            'connection_name' => $connectionName,
        ]);

        try {
            DB::connection($connectionName)->getPdo();

            return $result->ok();
        } catch (Exception $exception) {
            return $result->failed("Could not connect to the database: `{$exception->getMessage()}`");
        }
    }
}
