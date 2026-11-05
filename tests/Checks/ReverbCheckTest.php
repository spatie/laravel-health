<?php

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Spatie\Health\Checks\Checks\ReverbCheck;
use Spatie\Health\Enums\Status;

function fakeReverbServer(array $responses, array &$history = []): void
{
    $handler = HandlerStack::create(new MockHandler($responses));
    $handler->push(Middleware::history($history));

    config()->set('broadcasting.connections.reverb.client_options.handler', $handler);
}

beforeEach(function () {
    config()->set('broadcasting.connections.reverb', [
        'driver' => 'reverb',
        'key' => 'app-key',
        'secret' => 'app-secret',
        'app_id' => 'app-id',
        'options' => [
            'host' => 'reverb.test',
            'port' => 8080,
            'scheme' => 'http',
        ],
    ]);
});

it('will determine that a reachable reverb server without occupied channels is ok', function () {
    // Reverb returns an empty JSON array, not an empty object, when no channel is occupied.
    fakeReverbServer([new Response(200, [], '{"channels":[]}')]);

    $result = ReverbCheck::new()->run();

    expect($result->status)->toBe(Status::ok());
    expect($result->getShortSummary())->toBe('Connected');
    expect($result->meta)->toBe([
        'connection_name' => 'reverb',
        'host' => 'reverb.test',
    ]);
});

it('will determine that a reachable reverb server with occupied channels is ok', function () {
    fakeReverbServer([new Response(200, [], '{"channels":{"orders":{},"notifications":{}}}')]);

    $result = ReverbCheck::new()->run();

    expect($result->status)->toBe(Status::ok());
    expect($result->getShortSummary())->toBe('Connected');
});

it('will send a signed request using the broadcasting configuration', function () {
    $history = [];
    fakeReverbServer([new Response(200, [], '{"channels":[]}')], $history);

    ReverbCheck::new()->run();

    expect($history)->toHaveCount(1);

    $request = $history[0]['request'];
    parse_str($request->getUri()->getQuery(), $query);

    $expectedSignature = hash_hmac(
        'sha256',
        "GET\n/apps/app-id/channels\nauth_key=app-key&auth_timestamp={$query['auth_timestamp']}&auth_version=1.0",
        'app-secret',
    );

    expect($request->getMethod())->toBe('GET');
    expect((string) $request->getUri()->withQuery(''))->toBe('http://reverb.test:8080/apps/app-id/channels');
    expect($query['auth_signature'])->toBe($expectedSignature);
});

it('will use the configured timeout', function () {
    $history = [];
    fakeReverbServer([new Response(200, [], '{"channels":[]}')], $history);

    ReverbCheck::new()->timeout(3)->run();

    expect($history[0]['options']['timeout'])->toBe(3);
    expect($history[0]['options']['connect_timeout'])->toBe(3);
});

it('can use another broadcasting connection', function () {
    config()->set('broadcasting.connections.other-reverb', [
        'driver' => 'reverb',
        'key' => 'other-key',
        'secret' => 'other-secret',
        'app_id' => 'other-id',
        'options' => [
            'host' => 'other.test',
            'port' => 443,
            'scheme' => 'https',
        ],
    ]);

    $history = [];
    $handler = HandlerStack::create(new MockHandler([new Response(200, [], '{"channels":[]}')]));
    $handler->push(Middleware::history($history));
    config()->set('broadcasting.connections.other-reverb.client_options.handler', $handler);

    $result = ReverbCheck::new()->connectionName('other-reverb')->run();

    expect($result->status)->toBe(Status::ok());
    expect((string) $history[0]['request']->getUri()->withQuery(''))->toBe('https://other.test/apps/other-id/channels');
});

it('will fail when the broadcasting connection does not exist', function () {
    $result = ReverbCheck::new()->connectionName('unknown')->run();

    expect($result->status)->toBe(Status::failed());
    expect($result->getShortSummary())->toBe('Not configured');
});

it('will fail when the broadcasting connection does not use reverb', function () {
    config()->set('broadcasting.connections.log', ['driver' => 'log']);

    $result = ReverbCheck::new()->connectionName('log')->run();

    expect($result->status)->toBe(Status::failed());
    expect($result->getShortSummary())->toBe('Not configured');
});

it('will fail when a setting is missing', function (string $setting) {
    $history = [];
    fakeReverbServer([], $history);
    config()->set("broadcasting.connections.reverb.{$setting}", null);

    $result = ReverbCheck::new()->run();

    expect($result->status)->toBe(Status::failed());
    expect($result->getShortSummary())->toBe('Not configured');
    expect($result->getNotificationMessage())->toContain("`{$setting}`");
    expect($history)->toBeEmpty();
})->with(['app_id', 'key', 'secret', 'options.host']);

it('will fail when the reverb server is unreachable', function () {
    fakeReverbServer([
        new ConnectException('Connection refused', new Request('GET', 'http://reverb.test:8080')),
    ]);

    $result = ReverbCheck::new()->run();

    expect($result->status)->toBe(Status::failed());
    expect($result->getShortSummary())->toBe('Unreachable');
    expect($result->getNotificationMessage())->toContain('Connection refused');
});

it('will fail when the reverb server rejects the credentials', function (int $status) {
    fakeReverbServer([new Response($status, [], '{"error":"Authentication signature invalid."}')]);

    $result = ReverbCheck::new()->run();

    expect($result->status)->toBe(Status::failed());
    expect($result->getShortSummary())->toBe('Unauthorized');
})->with([401, 403]);

it('will fail when the reverb server returns an error', function () {
    fakeReverbServer([new Response(500)]);

    $result = ReverbCheck::new()->run();

    expect($result->status)->toBe(Status::failed());
    expect($result->getShortSummary())->toBe('Unexpected response');
    expect($result->getNotificationMessage())->toContain('500');
});
