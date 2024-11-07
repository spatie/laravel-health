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

        if (config('health.horizon.heartbeat_url')) {
            $this->pingUrl(config('health.horizon.heartbeat_url'));
        }

        return $result->ok()->shortSummary('Running');
    }
}
