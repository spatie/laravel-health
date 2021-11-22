<?php

namespace Spatie\Health\Checks;

use Exception;
use Illuminate\Support\Facades\DB;
use Spatie\Health\Models\CheckResultHistoryItem;
use Spatie\Health\Support\Result;

class DatabaseCheck extends Check
{
    protected string $connectionName = 'default';

    public function connectionName(string $connectionName)
    {
        $this->connectionName = $connectionName;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::make();

        try {
            DB::connection($this->connectionName)->getPdo();

            return $result->ok();
        } catch (Exception $exception) {
            return $result->failed("Could not connection to the database");
        }
    }
}
