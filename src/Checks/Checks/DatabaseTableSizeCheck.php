<?php

namespace Spatie\Health\Checks\Checks;

use Illuminate\Database\ConnectionResolverInterface;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Support\DbConnectionInfo;
use Spatie\Health\Traits\HasDatabaseConnection;

use function __;

class DatabaseTableSizeCheck extends Check
{
    use HasDatabaseConnection;

    /** @var array<string, int> */
    protected array $checkingTables = [];

    public function __construct()
    {
        parent::__construct();
        $this->label(__('health::checks.titles.database_table_size'));
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
                ->shortSummary(__('health::checks.database_table_size.sizes_ok'));
        }

        $tablesString = $tooBigTables->map(function (array $tableProperties) {
            return "`{$tableProperties['name']}` ({$tableProperties['actualSize']} MB)";
        })->join(', ', ' and ');

        $messageKey = $tooBigTables->count() === 1
            ? 'health::checks.database_table_size.single_table_too_big'
            : 'health::checks.database_table_size.multiple_tables_too_big';

        return $result->failed(__($messageKey, ['tables' => $tablesString]));
    }

    protected function getTableSizeInMb(string $tableName): float
    {
        $connectionName = $this->connectionName ?? $this->getDefaultConnectionName();

        $connection = app(ConnectionResolverInterface::class)->connection($connectionName);

        return (new DbConnectionInfo)->tableSizeInMb($connection, $tableName);
    }
}
