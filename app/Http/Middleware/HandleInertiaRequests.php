<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => fn () => $request->user() ? [
                'user' => array_merge($request->user()->only(
                    'id', 'name', 'email', 'profile_photo_path'
                ), [
                    'profile_photo_url' => $request->user()->profile_photo_path
                        ? asset('storage/' . $request->user()->profile_photo_path)
                        : null
                ])
            ] : null,
        ];
    }
}
