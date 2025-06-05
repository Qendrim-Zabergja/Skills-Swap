<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the DebugPusherChannels middleware
        $this->app['router']->aliasMiddleware('debug.pusher', \App\Http\Middleware\DebugPusherChannels::class);
        
        // Instead of using the default Broadcast::routes(), we'll define our own route
        // with explicit middleware and logging for debugging
        Route::post('/broadcasting/auth', function (\Illuminate\Http\Request $request) {
            // Log authentication attempt
            Log::info('Broadcasting auth request', [
                'user_authenticated' => auth()->check(),
                'user_id' => auth()->id(),
                'csrf_token' => $request->header('X-CSRF-TOKEN'),
                'channel' => $request->input('channel_name'),
                'socket_id' => $request->input('socket_id')
            ]);
            
            // Check if user is authenticated
            if (!auth()->check()) {
                Log::error('Broadcasting auth failed: User not authenticated');
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            
            // Handle the authentication request using the built-in broadcaster
            return Broadcast::auth($request);
        })->middleware(['web', 'auth', 'debug.pusher']);

        require base_path('routes/channels.php');
    }
}
