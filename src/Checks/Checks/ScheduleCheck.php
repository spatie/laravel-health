<?php

namespace Spatie\Health\Checks\Checks;

use Carbon\Carbon;
use Composer\InstalledVersions;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Traits\Pingable;

use function __;

class ScheduleCheck extends Check
{
    use Pingable;

    protected string $cacheKey = 'health:checks:schedule:latestHeartbeatAt';

    protected ?string $cacheStoreName = null;

    protected int $heartbeatMaxAgeInMinutes = 1;

    public function __construct()
    {
        parent::__construct();
        $this->label(__('health::checks.titles.schedule'));
    }

    public function useCacheStore(string $cacheStoreName): self
    {
        $this->cacheStoreName = $cacheStoreName;

        return $this;
    }

    public function getCacheStoreName(): string
    {
        return $this->cacheStoreName ?? config('cache.default');
    }

    public function cacheKey(string $cacheKey): self
    {
        $this->cacheKey = $cacheKey;

        return $this;
    }

    public function heartbeatMaxAgeInMinutes(int $heartbeatMaxAgeInMinutes): self
    {
        $this->heartbeatMaxAgeInMinutes = $heartbeatMaxAgeInMinutes;

        return $this;
    }

    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    public function run(): Result
    {
        $result = Result::make()->ok();

        $lastHeartbeatTimestamp = cache()->store($this->cacheStoreName)->get($this->cacheKey);

        if (! $lastHeartbeatTimestamp) {
            return $result->failed(__('health::checks.schedule.not_running'));
        }

        $latestHeartbeatAt = Carbon::createFromTimestamp($lastHeartbeatTimestamp);

        $carbonVersion = InstalledVersions::getVersion('nesbot/carbon');

        $minutesAgo = $latestHeartbeatAt->diffInMinutes();

        if (version_compare(
            $carbonVersion,
            '3.0.0',
            '<'
        )) {
            $minutesAgo += 1;
        }

        if ($minutesAgo > $this->heartbeatMaxAgeInMinutes) {
            return $result->failed(__('health::checks.schedule.last_run_too_long_ago', [
                'minutes' => $minutesAgo,
            ]));
        }

        if (config('health.schedule.heartbeat_url')) {
            $this->pingUrl(config('health.schedule.heartbeat_url'));
        }

        return $result;
    }
}
