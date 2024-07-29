<?php

namespace Spatie\Health\Checks\Checks;

use Illuminate\Database\ConnectionResolverInterface;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Support\DbConnectionInfo;

class DatabaseTableSizeCheck extends Check
{
    protected ?string $connectionName = null;

    /** @var array<string, int> */
    protected array $checkingTables = [];

    public function connectionName(string $connectionName): self
    {
        $this->connectionName = $connectionName;

        return $this;
    }

    public function table(string $name, int $maxSizeInMb): self
    {
        $this->checkingTables[$name] = $maxSizeInMb;

        return $this;
    }

    public function run(): Result
    {
        $tableSizes = collect($this->checkingTables)
            ->map(function (int $maxSizeInMb, string $tableName) {
                return [
                    'name' => $tableName,
                    'actualSize' => $this->getTableSizeInMb($tableName),
                    'maxSize' => $maxSizeInMb,
                ];
            });

        $result = Result::make()->meta($tableSizes->toArray());

        $tooBigTables = $tableSizes->filter(
            fn (array $tableProperties) => $tableProperties['actualSize'] > $tableProperties['maxSize']
        );

        if ($tooBigTables->isEmpty()) {
            return $result
                ->ok()
                ->shortSummary('Table sizes are ok');
        }

        $tablesString = $tooBigTables->map(function (array $tableProperties) {
            return "`{$tableProperties['name']}` ({$tableProperties['actualSize']} MB)";
        })->join(', ', ' and ');

        $messageStart = $tooBigTables->count() === 1
            ? 'This table is'
            : 'These tables are';

        $message = "{$messageStart} too big: {$tablesString}";

        return $result->failed($message);
    }

    protected function getDefaultConnectionName(): string
    {
        return config('database.default');
    }

    protected function getTableSizeInMb(string $tableName): float
    {
        $connectionName = $this->connectionName ?? $this->getDefaultConnectionName();

        $connection = app(ConnectionResolverInterface::class)->connection($connectionName);

        return (new DbConnectionInfo)->tableSizeInMb($connection, $tableName);
    }
}
