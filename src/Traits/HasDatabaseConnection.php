<?php

namespace Spatie\Health\Traits;

trait HasDatabaseConnection
{
    protected ?string $connectionName = null;

    public function connectionName(string $connectionName): self
    {
        $this->connectionName = $connectionName;

        return $this;
    }

    protected function getDefaultConnectionName(): string
    {
        return config('database.default');
    }
}
