<?php

namespace Spatie\Health\Checks\Checks;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Pusher\ApiErrorException;
use Pusher\Pusher;
use Pusher\PusherException;
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
        if ($seconds < 1) {
            throw new InvalidArgumentException('The Reverb check timeout must be at least one second.');
        }

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
            $value = Arr::get($config, $setting);

            if ((! is_string($value) && ! is_int($value)) || trim((string) $value) === '') {
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
            // `getChannels()` can't be used: when no channel is occupied, Reverb returns
            // `{"channels": []}` instead of `{"channels": {}}`, which makes it crash.
            $response = $this->pusher($config)->get('/channels');

            if (! is_object($response) || ! isset($response->channels) || (! is_array($response->channels) && ! is_object($response->channels))) {
                return $result
                    ->failed('The Reverb server did not return a channel listing.')
                    ->shortSummary('Unexpected response');
            }
        } catch (ApiErrorException $exception) {
            if (in_array($exception->getCode(), [401, 403])) {
                return $result
                    ->failed("The Reverb server rejected the credentials of broadcasting connection `{$this->connectionName}`.")
                    ->shortSummary('Unauthorized');
            }

            return $result
                ->failed("The Reverb server returned an unexpected status code `{$exception->getCode()}`.")
                ->shortSummary('Unexpected response');
        } catch (PusherException) {
            return $result
                ->failed('The Reverb server did not return a valid channel listing.')
                ->shortSummary('Unexpected response');
        } catch (GuzzleException) {
            return $result
                ->failed('Could not reach the Reverb server.')
                ->shortSummary('Unreachable');
        } catch (Throwable) {
            return $result
                ->failed('An exception occurred when checking the Reverb server.')
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
