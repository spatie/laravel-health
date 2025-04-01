<?php

namespace Spatie\Health\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequiresSecretToken
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            config('health.secret_token')
            && ($request->headers->get('X-Secret-Token') !== config('health.secret_token'))
        ) {
            abort(403, 'Incorrect secret token');
        }

        return $next($request);
    }
}
