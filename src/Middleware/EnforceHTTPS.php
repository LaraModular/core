<?php

namespace LaraModule\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceHTTPS
{
    /**
     * Enforce HTTPS in production environment.
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (app()->environment('production') && ! $request->secure()) {
            return response()->json([
                'message' => 'HTTPS required',
            ], 426);
        }

        return $next($request);
    }
}
