<?php

namespace Spatie\Health\Checks\Checks;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Support\Arr;
use Pusher\ApiErrorException;
use Pusher\Pusher;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Throwable;

class ReverbCheck extends Check
{
    protected string $connectionName = 'reverb';

    protected int $timeout = 1;

    public function connectionName(string $connectionName): self
    {
        $this->connectionName = $connectionName;

        return $this;
    }

    public function timeout(int $seconds): self
    {
        $this->timeout = $seconds;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::make()->meta([
            'connection_name' => $this->connectionName,
        ]);

        $config = config("broadcasting.connections.{$this->connectionName}");

        if (! is_array($config) || ($config['driver'] ?? null) !== 'reverb') {
            return $result
                ->failed("The broadcasting connection `{$this->connectionName}` does not exist or does not use the `reverb` driver.")
                ->shortSummary('Not configured');
        }

        foreach (['app_id', 'key', 'secret', 'options.host'] as $setting) {
            if ((string) Arr::get($config, $setting) === '') {
                return $result
                    ->failed("The `{$setting}` of broadcasting connection `{$this->connectionName}` is not set.")
                    ->shortSummary('Not configured');
            }
        }

        if (! class_exists(Pusher::class)) {
            return $result
                ->failed('The `pusher/pusher-php-server` package is required to check the Reverb server.')
                ->shortSummary('Not configured');
        }

        $result->appendMeta(['host' => (string) Arr::get($config, 'options.host')]);

        try {
            $this->pusher($config)->getChannels();
        } catch (ApiErrorException $exception) {
            if (in_array($exception->getCode(), [401, 403])) {
                return $result
                    ->failed("The Reverb server rejected the credentials of broadcasting connection `{$this->connectionName}`.")
                    ->shortSummary('Unauthorized');
            }

            return $result
                ->failed("The Reverb server returned an unexpected status code `{$exception->getCode()}`.")
                ->shortSummary('Unexpected response');
        } catch (GuzzleException $exception) {
            return $result
                ->failed("Could not reach the Reverb server: `{$exception->getMessage()}`")
                ->shortSummary('Unreachable');
        } catch (Throwable $exception) {
            return $result
                ->failed("An exception occurred when checking the Reverb server: `{$exception->getMessage()}`")
                ->shortSummary('Failed');
        }

        return $result
            ->ok()
            ->shortSummary('Connected');
    }

    /**
     * Build the Pusher client the same way Laravel does for broadcasting, so the
     * check uses the exact same settings as the application, except the timeouts.
     *
     * @param  array<string, mixed>  $config
     */
    protected function pusher(array $config): Pusher
    {
        $config['options']['timeout'] = $this->timeout;

        $config['client_options'] = array_merge($config['client_options'] ?? [], [
            'connect_timeout' => $this->timeout,
            'timeout' => $this->timeout,
        ]);

        return app(BroadcastManager::class)->pusher($config);
    }
}
