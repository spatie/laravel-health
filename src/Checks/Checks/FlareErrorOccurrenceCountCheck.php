<?php

namespace Spatie\Health\Checks\Checks;

use Illuminate\Support\Facades\Http;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class FlareErrorOccurrenceCountCheck extends Check
{
    protected int $warningThreshold = 500;
    protected int $errorThreshold = 1000;
    protected int $periodInMinutes = 60;
    protected ?int $flareProjectId = null;
    protected ?string $flareApiToken = null;

    public function warnWhenMoreErrorsReceivedThan(int $warningThreshold): self
    {
        $this->warningThreshold = $warningThreshold;

        return $this;
    }

    public function failWhenMoreErrorsReceivedThan(int $errorThreshold): self
    {
        $this->errorThreshold = $errorThreshold;

        return $this;
    }

    public function periodInMinutes(int $periodInMinutes): self
    {
        $this->periodInMinutes = $periodInMinutes;

        return $this;
    }

    public function apiToken(string $apiToken): self
    {
        $this->flareApiToken = $apiToken;

        return $this;
    }

    public function projectId(string $projectId): self
    {
        $this->flareProjectId = $projectId;

        return $this;
    }

    public function run(): Result
    {
        $errorOccurrenceCount = $this->getFlareErrorOccurrenceCount();


        $result = Result::make()->ok()->meta([
            'count' => 0,
        ]);

        $message = "In the past {$this->periodInMinutes} minutes, {$errorOccurrenceCount} errors occurred.";

        if ($errorOccurrenceCount > $this->errorThreshold) {
            return $result->failed($message);
        }

        if ($errorOccurrenceCount > $this->warningThreshold) {
            return $result->warning($message);
        }

        return $result;
    }

    protected function getFlareErrorOccurrenceCount(): int
    {
        $startDate = now()->subMinutes($this->periodInMinutes)->format('Y-m-d H:i:s');
        $endDate = now()->format('Y-m-d H:i:s');

        $apiToken = $this->flareApiToken ?? env('FLARE_API_TOKEN');
        $flareProjectId = $this->flareApiToken ?? env('FLARE_PROJECT_ID');

        return Http::get("https://flareapp.io/api/project/{$flareProjectId}?start_date={$startDate}&end_date={$endDate}&api_token={$apiToken}")

            ->json('count', 0);
    }
}
