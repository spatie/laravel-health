<?php

namespace Spatie\Health\Checks\Checks;

use Illuminate\Database\ConnectionResolverInterface;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Support\DbConnectionInfo;

class DatabaseSizeCheck extends Check
{
    protected ?string $connectionName = null;

	protected float $failWhenSizeAboveGb = 1;

    public function connectionName(string $connectionName): self
    {
        $this->connectionName = $connectionName;

        return $this;
    }

	public function failWhenSizeAboveGb(float $errorThresholdGb): self
	{
		$this->failWhenSizeAboveGb = $errorThresholdGb;

		return $this;
	}

    public function run(): Result
    {
		$result = Result::make();

		$databaseSizeInGb = $this->getDatabaseSizeInGb();

		return $databaseSizeInGb >= $this->failWhenSizeAboveGb
			? $result->failed("Database size is {$databaseSizeInGb} GB, which is above the threshold of {$this->failWhenSizeAboveGb} GB")
			: $result->ok("{$databaseSizeInGb} GB");
    }

    protected function getDefaultConnectionName(): string
    {
        return config('database.default');
    }

    protected function getDatabaseSizeInGb(): float
    {
        $connectionName = $this->connectionName ?? $this->getDefaultConnectionName();

        $connection = app(ConnectionResolverInterface::class)->connection($connectionName);

        return round((new DbConnectionInfo())->databaseSizeInMb($connection) / 1000, 2);
    }
}
