<?php

namespace Spatie\Health\Http\Middleware;

use Closure;
use Spatie\Health\Checks\Check;
use Spatie\Health\Facades\Health;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HealthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        Health::registeredChecks()->each(
            fn (Check $check) => $check->onTerminate($request, $response)
        );
    }
}
