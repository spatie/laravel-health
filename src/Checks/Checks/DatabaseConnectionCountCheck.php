<?php

namespace Spatie\Health\Checks\Checks;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\Str;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Support\DbConnectionInfo;
use Spatie\Health\Traits\HasDatabaseConnection;

use function __;

class DatabaseConnectionCountCheck extends Check
{
    use HasDatabaseConnection;

    protected ?int $warningThreshold = null;

    protected int $errorThreshold = 50;

    public function __construct()
    {
        parent::__construct();
        $this->label(__('health::checks.titles.database_connection_count'));
    }

    public function warnWhenMoreConnectionsThan(int $warningThreshold): self
    {
        $this->warningThreshold = $warningThreshold;

        return $this;
    }

    public function failWhenMoreConnectionsThan(int $errorThreshold): self
    {
        $this->errorThreshold = $errorThreshold;

        return $this;
    }

    public function run(): Result
    {
        $connectionCount = $this->getConnectionCount();

        $shortSummary = $connectionCount.' '.Str::plural('connection', $connectionCount);

        $result = Result::make()
            ->ok()
            ->meta(['connection_count' => $connectionCount])
            ->shortSummary($shortSummary);

        if ($connectionCount > $this->errorThreshold) {
            return $result->failed(__('health::checks.database_connection_count.too_many_connections', [
                'count' => $connectionCount,
            ]));
        }

        if (! is_null($this->warningThreshold)) {
            if ($connectionCount > $this->warningThreshold) {
                return $result->warning($shortSummary);
            }
        }

        return $result;
    }

    protected function getConnectionCount(): int
    {
        $connectionName = $this->connectionName ?? $this->getDefaultConnectionName();

        $connection = app(ConnectionResolverInterface::class)->connection($connectionName);

        return (new DbConnectionInfo)->connectionCount($connection);
    }
}
