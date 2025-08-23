<?php

namespace Spatie\Health\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequiresBearerToken
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('health.nagios_endpoint.bearer_token')) {
            $bearerToken = $this->getBearerToken($request);

            if ($bearerToken !== config('health.nagios_endpoint.bearer_token')) {
                abort(403, 'Incorrect bearer token');
            }
        }

        return $next($request);
    }

    private function getBearerToken(Request $request): ?string
    {
        $authorization = $request->headers->get('Authorization');

        if ($authorization && str_starts_with($authorization, 'Bearer ')) {
            return substr($authorization, 7);
        }

        return null;
    }
}
