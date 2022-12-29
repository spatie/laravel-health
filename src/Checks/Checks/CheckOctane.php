<?php

namespace Spatie\Health\Checks\Checks;

use Laravel\Octane\RoadRunner\ServerProcessInspector as RoadRunnerServerProcessInspector;
use Laravel\Octane\Swoole\ServerProcessInspector as SwooleServerProcessInspector;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class CheckOctane extends Check
{
    protected string $server = 'swoole';

    protected ?string $name = 'Octane';

    public function run(): Result
    {
        $result = Result::make();

        try {
            $server = $this->server ?: config('octane.server');

            $isRunning = match ($server) {
                'swoole' => $this->isSwooleServerRunning(),
                'roadrunner' => $this->isRoadRunnerServerRunning(),
                default => $this->invalidServer($result, $server),
            };
        } catch (\Exception) {
            return $result->failed('Octane does not seem to be installed correctly.');
        }

        if (!$isRunning) {
            return $result
                ->failed('Octane Server is not Running')
                ->shortSummary('Not Running');
        }

        return $result
            ->ok()
            ->shortSummary('Octane Server is Running');
    }

    public function setServer(string $server)
    {
        $this->server = $server;

        return $this;
    }

    protected function isSwooleServerRunning()
    {
        return app(SwooleServerProcessInspector::class)
            ->serverIsRunning();
    }

    /**
     * Check if the RoadRunner server is running.
     *
     * @return bool
     */
    protected function isRoadRunnerServerRunning()
    {
        return app(RoadRunnerServerProcessInspector::class)
            ->serverIsRunning();
    }

    protected function invalidServer(Result $result, string $server): Result
    {
        return $result
            ->failed('Octane Server is not valid')
            ->shortSummary('Not valid');
    }
}
