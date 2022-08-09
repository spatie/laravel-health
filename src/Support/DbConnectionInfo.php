<?php

namespace Spatie\Health\Support;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Spatie\Health\Exceptions\DatabaseNotSupported;

class DbConnectionInfo
{
    public function connectionCount(ConnectionInterface $connection): int
    {
        return match (true) {
            $connection instanceof MySqlConnection => (int)$connection->selectOne($connection->raw('show status where variable_name = "threads_connected"'))->Value,
            $connection instanceof PostgresConnection => (int)$connection->selectOne('select count(*) as connections from pg_stat_activity')->connections,
            default => throw new DatabaseNotSupported()
        };
    }

    public function tableSizeInMb(ConnectionInterface $connection, string $table): float
    {
        $sizeInBytes = match (true) {
            $connection instanceof MySqlConnection => $this->getMySQLTableSize($connection, $table),
            $connection instanceof PostgresConnection => $this->getPostgresTableSize($connection, $table),
            default => throw DatabaseNotSupported::make($connection),
        };

        return $sizeInBytes / 1024 / 1024;
    }

    protected function getMySQLTableSize(ConnectionInterface $connection, string $table): int
    {
        return $connection->selectOne('SELECT (data_length + index_length) AS size FROM information_schema.TABLES WHERE table_schema = ? AND table_name = ?', [
            $connection->getDatabaseName(),
            $table,
        ])->size;
    }

    protected function getPostgresTableSize(ConnectionInterface $connection, string $table): int
    {
        return $connection->selectOne('SELECT pg_total_relation_size(?) AS size;', [
            $table,
        ])->size;
    }
}
