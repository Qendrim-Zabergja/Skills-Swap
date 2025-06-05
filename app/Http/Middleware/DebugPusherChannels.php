<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugPusherChannels
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
        // Log detailed information about the Pusher channel auth request
        Log::info('Pusher Channel Auth Request', [
            'channel_name' => $request->input('channel_name'),
            'socket_id' => $request->input('socket_id'),
            'auth_key' => $request->input('auth'),
            'user_id' => auth()->id(),
            'headers' => [
                'x_csrf_token' => $request->header('X-CSRF-TOKEN'),
                'x_requested_with' => $request->header('X-Requested-With'),
                'accept' => $request->header('Accept'),
                'content_type' => $request->header('Content-Type'),
            ],
            'method' => $request->method(),
            'path' => $request->path(),
            'is_authenticated' => auth()->check(),
        ]);

        // Continue with the request
        $response = $next($request);
        
        // Log the response status
        Log::info('Pusher Channel Auth Response', [
            'status' => $response->getStatusCode(),
            'content' => $response->getContent(),
        ]);
        
        return $response;
    }
}
