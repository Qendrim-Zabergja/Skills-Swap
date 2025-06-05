<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugBroadcasting
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Log the request details for debugging
        Log::info('Broadcasting auth request', [
            'path' => $request->path(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'user_authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'csrf_token_in_request' => $request->header('X-CSRF-TOKEN'),
            'csrf_token_in_session' => csrf_token(),
        ]);

        return $next($request);
    }
}
