<?php

namespace LaraModule\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class ApiLocale
{
    /**
     * Set app locale based on the Accept-Language header.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Accept-Language');
        if ($header) {
            $locale = substr($header, 0, 2);
            App::setLocale($locale);
        }

        return $next($request);
    }
}
