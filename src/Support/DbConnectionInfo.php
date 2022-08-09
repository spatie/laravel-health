<?php

namespace Spatie\Health\Support;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Spatie\Health\Exceptions\DatabaseNotSupported;

class DbConnectionInfo
{
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
