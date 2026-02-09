<?php

namespace Spatie\Health\Checks\Checks;

use Exception;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Traits\HasFailedAfter;
use Spatie\Health\Traits\Pingable;

class HorizonCheck extends Check
{
    use HasFailedAfter;
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
        try {
            $horizon = app(MasterSupervisorRepository::class);
        } catch (Exception) {
            return Result::make()
                ->failed('Horizon does not seem to be installed correctly.');
        }

        $masterSupervisors = $horizon->all();

        if (count($masterSupervisors) === 0) {
            return $this
                ->handleFailure('Horizon is not running.')
                ->shortSummary('Not running');
        }

        $masterSupervisor = $masterSupervisors[0];

        if ($masterSupervisor->status === 'paused') {
            return Result::make()
                ->warning('Horizon is running, but the status is paused.')
                ->shortSummary('Paused');
        }

        $heartbeatUrl = $this->heartbeatUrl ?? config('health.horizon.heartbeat_url');

        if ($heartbeatUrl) {
            $this->pingUrl($heartbeatUrl);
        }

        return $this
            ->handleSuccess()
            ->shortSummary('Running');
    }
}
