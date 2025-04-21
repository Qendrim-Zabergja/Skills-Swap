<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Skill;
use App\Models\SwapRequest;
use App\Models\Message;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        
        return Inertia::render('Profile/Edit', [
            'auth' => [
                'user' => array_merge($user->toArray(), [
                    'profile_photo_url' => $user->profile_photo_url,
                ]),
            ],
            'skills' => $user->skills()->get(),
            'incomingRequests' => SwapRequest::with('user')
                ->where('recipient_id', $user->id)
                ->where('status', 'pending')
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'user' => [
                            'id' => $request->user->id,
                            'name' => $request->user->name,
                            'profile_photo_url' => $request->user->profile_photo_url,
                        ],
                        'skill_wanted' => $request->skill_wanted,
                        'skill_offered' => $request->skill_offered,
                    ];
                }),
            'outgoingRequests' => SwapRequest::with('recipient')
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'recipient' => [
                            'id' => $request->recipient->id,
                            'name' => $request->recipient->name,
                            'profile_photo_url' => $request->recipient->profile_photo_url,
                        ],
                        'skill_wanted' => $request->skill_wanted,
                        'skill_offered' => $request->skill_offered,
                    ];
                }),
            'messages' => Message::with('user')
                ->where('recipient_id', $user->id)
                ->orWhere('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy(function ($message) use ($user) {
                    return $message->user_id === $user->id ? $message->recipient_id : $message->user_id;
                })
                ->map(function ($messages) use ($user) {
                    $lastMessage = $messages->first();
                    $otherUser = $lastMessage->user_id === $user->id ? $lastMessage->recipient : $lastMessage->user;
                    
                    return [
                        'id' => $lastMessage->id,
                        'user' => [
                            'id' => $otherUser->id,
                            'name' => $otherUser->name,
                            'profile_photo_url' => $otherUser->profile_photo_url,
                        ],
                        'skill_exchange' => $lastMessage->skill_exchange,
                        'last_message' => $lastMessage->content,
                        'time' => Carbon::parse($lastMessage->created_at)->diffForHumans(),
                    ];
                })
                ->values(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:1024'],
        ]);

        $path = $request->file('photo')->store('profile-photos', 'public');
        $request->user()->update(['profile_photo_path' => $path]);

        return Redirect::route('profile.edit');
    }
}