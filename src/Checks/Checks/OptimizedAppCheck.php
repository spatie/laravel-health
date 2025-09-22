<?php

namespace Spatie\Health\Checks\Checks;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

use function __;

class OptimizedAppCheck extends Check
{
    public const CONFIG = 'config';

    public const ROUTES = 'routes';

    public const EVENTS = 'events';

    /** @var array<string>|null */
    public ?array $checks = null;

    public function __construct()
    {
        parent::__construct();
        $this->label(__('health::checks.titles.optimized_app'));
    }

    public function run(): Result
    {
        $result = Result::make();

        if ($this->shouldPerformCheck(self::CONFIG)) {
            if (! app()->configurationIsCached()) {
                return $result->failed(__('health::checks.optimized_app.config_not_cached'));
            }
        }

        if ($this->shouldPerformCheck(self::ROUTES)) {
            if (! app()->routesAreCached()) {
                return $result->failed(__('health::checks.optimized_app.routes_not_cached'));
            }
        }

        if ($this->shouldPerformCheck(self::EVENTS)) {
            if (! app()->eventsAreCached()) {
                return $result->failed(__('health::checks.optimized_app.events_not_cached'));
            }
        }

        return $result->ok();
    }

    public function checkConfig(): self
    {
        return $this->addCheck(self::CONFIG);
    }

    public function checkRoutes(): self
    {
        return $this->addCheck(self::ROUTES);
    }

    public function checkEvents(): self
    {
        return $this->addCheck(self::EVENTS);
    }

    protected function addCheck(string $check): self
    {
        if ($this->checks === null) {
            $this->checks = [];
        }

        $this->checks[] = $check;

        return $this;
    }

    protected function shouldPerformCheck(string $check): bool
    {
        if ($this->checks === null) {
            return true;
        }

        return in_array($check, $this->checks);
    }
}
