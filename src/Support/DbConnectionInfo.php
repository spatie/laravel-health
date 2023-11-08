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
            $connection instanceof MySqlConnection => (int) $connection->selectOne('SELECT COUNT(*) FROM information_schema.PROCESSLIST')->{'COUNT(*)'},
            $connection instanceof PostgresConnection => (int) $connection->selectOne('select count(*) as connections from pg_stat_activity')->connections,
            default => throw DatabaseNotSupported::make($connection),
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

    public function databaseSizeInMb(ConnectionInterface $connection): float
    {
        return match (true) {
            $connection instanceof MySqlConnection => $this->getMySQlDatabaseSize($connection),
            $connection instanceof PostgresConnection => $this->getPostgresDatabaseSize($connection),
            default => throw DatabaseNotSupported::make($connection),
        };
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

    protected function getMySQLDatabaseSize(ConnectionInterface $connection): int
    {
        return $connection->selectOne('SELECT size from (SELECT table_schema "name", ROUND(SUM(data_length + index_length) / 1024 / 1024) as size FROM information_schema.tables GROUP BY table_schema) alias_one where name = ?', [
            $connection->getDatabaseName(),
        ])->size;
    }

    protected function getPostgresDatabaseSize(ConnectionInterface $connection): int
    {
        return $connection->selectOne('SELECT pg_database_size(?) / 1024 / 1024 AS size;', [
            $connection->getDatabaseName(),
        ])->size;
    }
}
