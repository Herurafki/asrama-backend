<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected function redirectTo($request): ?string
    {
        // Kalau request bukan json, lempar 401
        if (! $request->expectsJson()) {
            abort(401, 'Unauthorized.');
        }

        return null;
    }
}
