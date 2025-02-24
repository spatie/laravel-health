<?php

namespace Spatie\Health\Checks\Checks;

use Exception;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Traits\Pingable;

class HorizonCheck extends Check
{
    use Pingable;

    protected ?string $heartbeatUrl = null;

    /**
     * Optional setter. If a consumer of the class calls it, the provided
     * URL will override the default config URL in run().
     */
    public function heartbeatUrl(string $url): self
    {
        $this->heartbeatUrl = $url;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::make();

        try {
            $horizon = app(MasterSupervisorRepository::class);
        } catch (Exception) {
            return $result->failed('Horizon does not seem to be installed correctly.');
        }

        $masterSupervisors = $horizon->all();

        if (count($masterSupervisors) === 0) {
            return $result
                ->failed('Horizon is not running.')
                ->shortSummary('Not running');
        }

        $masterSupervisor = $masterSupervisors[0];

        if ($masterSupervisor->status === 'paused') {
            return $result
                ->warning('Horizon is running, but the status is paused.')
                ->shortSummary('Paused');
        }

        $heartbeatUrl = $this->heartbeatUrl ?? config('health.horizon.heartbeat_url');

        if ($heartbeatUrl) {
            $this->pingUrl($heartbeatUrl);
        }

        return $result->ok()->shortSummary('Running');
    }
}
