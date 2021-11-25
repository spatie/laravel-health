<?php

namespace Spatie\Health\Checks\Checks;

use Exception;
use Illuminate\Support\Facades\DB;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class DatabaseCheck extends Check
{
    protected string $connectionName = 'default';

    public function connectionName(string $connectionName): self
    {
        $this->connectionName = $connectionName;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::make()->meta([
            'connection_name' => $this->connectionName,
        ]);

        try {
            DB::connection($this->connectionName)->getPdo();

            return $result->ok();
        } catch (Exception $exception) {
            return $result->failed("Could not connect to the database: `{$exception->getMessage()}`");
        }
    }
}
