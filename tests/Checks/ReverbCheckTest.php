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
    config()->set('broadcasting.connections.reverb.options.timeout', 30);
    config()->set('broadcasting.connections.reverb.client_options.timeout', 40);
    config()->set('broadcasting.connections.reverb.client_options.connect_timeout', 50);
    config()->set('broadcasting.connections.reverb.client_options.verify', '/custom/ca.pem');

    $result = ReverbCheck::new()->timeout(3)->run();

    expect($result->status)->toBe(Status::ok());
    expect($history[0]['options']['timeout'])->toBe(3);
    expect($history[0]['options']['connect_timeout'])->toBe(3);
    expect($history[0]['options']['verify'])->toBe('/custom/ca.pem');
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
    expect($result->getNotificationMessage())->toBe('Could not reach the Reverb server.');
});

it('will not expose the request signature when the reverb server is unreachable', function () {
    $url = 'http://reverb.test:8080/apps/app-id/channels?auth_key=app-key&auth_timestamp=1700000000&auth_version=1.0&auth_signature=abc123';

    fakeReverbServer([
        new ConnectException(
            "cURL error 7: Failed to connect to reverb.test port 8080: Couldn't connect to server (see https://curl.haxx.se/libcurl/c/libcurl-errors.html) for {$url}",
            new Request('GET', $url),
        ),
    ]);

    $message = ReverbCheck::new()->run()->getNotificationMessage();

    expect($message)
        ->toBe('Could not reach the Reverb server.')
        ->not->toContain('auth_key')
        ->not->toContain('auth_signature');
});

it('will fail when the reverb server rejects the credentials', function (int $status) {
    fakeReverbServer([new Response($status, [], '{"error":"Authentication signature invalid."}')]);

    $result = ReverbCheck::new()->run();

    expect($result->status)->toBe(Status::failed());
    expect($result->getShortSummary())->toBe('Unauthorized');
})->with([401, 403]);

it('will fail when the reverb server returns an unexpected status', function (int $status) {
    fakeReverbServer([new Response($status, [], 'app-secret auth_signature=abc123')]);

    $result = ReverbCheck::new()->run();

    expect($result->status)->toBe(Status::failed());
    expect($result->getShortSummary())->toBe('Unexpected response');
    expect($result->getNotificationMessage())
        ->toContain((string) $status)
        ->not->toContain('app-secret', 'abc123');
})->with([204, 404, 500]);

it('will reject responses that are not channel listings', function (string $body) {
    fakeReverbServer([new Response(200, [], $body)]);

    $result = ReverbCheck::new()->run();

    expect($result->status)->toBe(Status::failed());
    expect($result->getShortSummary())->toBe('Unexpected response');
})->with(['{}', 'null', 'true', '{"channels":null}', '{"channels":"ok"}', '<html>Login</html>']);

it('will not expose credentials from transport or client exceptions', function (bool $transport) {
    $message = 'app-secret app-key https://user:password@reverb.test/?auth_signature=abc123';
    $exception = $transport
        ? new ConnectException($message, new Request('GET', 'http://reverb.test'))
        : new RuntimeException($message);
    fakeReverbServer([$exception]);

    $result = ReverbCheck::new()->run();

    expect($result->status)->toBe(Status::failed());
    expect($result->getNotificationMessage())
        ->not->toContain('app-secret', 'app-key', 'password', 'abc123');
})->with([true, false]);

it('will reject malformed required settings without throwing', function (string $setting, mixed $value) {
    config()->set("broadcasting.connections.reverb.{$setting}", $value);

    $result = ReverbCheck::new()->run();

    expect($result->status)->toBe(Status::failed());
    expect($result->getShortSummary())->toBe('Not configured');
})->with([
    ['secret', ['sensitive']],
    ['key', new stdClass],
    ['app_id', false],
    ['options.host', ['invalid']],
    ['options.host', '   '],
]);

it('requires a positive timeout', function (int $seconds) {
    expect(fn () => ReverbCheck::new()->timeout($seconds))->toThrow(InvalidArgumentException::class);
})->with([0, -1]);
