<?php

namespace Spatie\Health\Checks\Checks;

use Exception;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Traits\Pingable;

use function __;

class HorizonCheck extends Check
{
    use Pingable;

    protected ?string $heartbeatUrl = null;

    public function __construct()
    {
        parent::__construct();
        $this->label(__('health::checks.titles.horizon'));
    }


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
            return $result->failed(__('health::checks.horizon.not_installed'));
        }

        $masterSupervisors = $horizon->all();

        if (count($masterSupervisors) === 0) {
            return $result
                ->failed(__('health::checks.horizon.not_running'))
                ->shortSummary(__('health::checks.horizon.not_running_short'));
        }

        $masterSupervisor = $masterSupervisors[0];

        if ($masterSupervisor->status === 'paused') {
            return $result
                ->warning(__('health::checks.horizon.paused'))
                ->shortSummary(__('health::checks.horizon.paused_short'));
        }

        $heartbeatUrl = $this->heartbeatUrl ?? config('health.horizon.heartbeat_url');

        if ($heartbeatUrl) {
            $this->pingUrl($heartbeatUrl);
        }

        return $result->ok()->shortSummary(__('health::checks.horizon.running'));
    }
}
