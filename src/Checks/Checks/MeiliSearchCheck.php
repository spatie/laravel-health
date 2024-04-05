<?php

namespace Spatie\Health\Checks\Checks;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class MeiliSearchCheck extends Check
{
    protected int $timeout = 1;

    protected string $url = 'http://127.0.0.1:7700/health';

    protected ?string $token = null;

    public function timeout(int $seconds): self
    {
        $this->timeout = $seconds;

        return $this;
    }

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function token(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->getName();
    }

    public function run(): Result
    {
        try {
            $response = Http::timeout($this->timeout)
                ->when($this->token !== null, fn ($r) => $r->withToken($this->token))
                ->asJson()
                ->get($this->url);
        } catch (Exception) {
            return Result::make()
                ->failed()
                ->shortSummary('Unreachable')
                ->notificationMessage("Could not reach {$this->url}.");
        }

        /** @phpstan-ignore-next-line */
        if (! $response) {
            return Result::make()
                ->failed()
                ->shortSummary('Did not respond')
                ->notificationMessage("Did not get a response from {$this->url}.");
        }

        if (! Arr::has($response, 'status')) {
            return Result::make()
                ->failed()
                ->shortSummary('Invalid response')
                ->notificationMessage('The response did not contain a `status` key.');
        }

        $status = Arr::get($response, 'status');

        if (! in_array($status, ['available', 'running'])) {
            return Result::make()
                ->failed()
                ->shortSummary(ucfirst($status))
                ->notificationMessage("The health check returned a status `{$status}`.");
        }

        return Result::make()
            ->ok()
            ->shortSummary(ucfirst($status));
    }
}
