<?php

namespace Spatie\Health\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequiresSecret
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $secret = $request->headers->get('oh-dear-health-check-secret')) {
            abort(403, 'Secret header not set');
        }

        if ($secret !== config('health.oh_dear_endpoint.secret')) {
            abort(403, 'Incorrect secret');
        }

        return $next($request);
    }
}
