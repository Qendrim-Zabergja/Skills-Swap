<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Rating;
use App\Models\SwapRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page.
     */
    public function edit(Request $request): Response
    {
        $user           = $request->user();
        $teachingSkills = $user->skills()->where('type', 'teach')->get();
        $learningSkills = $user->skills()->where('type', 'learn')->get();

        $incomingRequests = SwapRequest::with(['user', 'ratings'])
            ->where('recipient_id', $user->id)
            ->whereIn('status', ['Pending', 'Accepted'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) use ($user) {
                // Mark as rated if the current user has rated this request
                $request->rated = $request->ratings->where('rater_id', $user->id)->isNotEmpty();
                return $request;
            });

        $outgoingRequests = SwapRequest::with(['recipient', 'ratings'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['Pending', 'Accepted', 'Declined', 'Cancelled'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) use ($user) {
    // Mark as rated if the current user has rated this request
    $request->rated = $request->ratings->where('rater_id', $user->id)->isNotEmpty();
    return $request;
});

        return Inertia::render('Profile/Edit', [
            'user'             => array_merge($user->toArray(), [
                'profile_photo_url' => $user->profile_photo_path
                ? asset('storage/' . $user->profile_photo_path)
                : null,
            ]),
            'teachingSkills'   => $teachingSkills,
            'learningSkills'   => $learningSkills,
            'incomingRequests' => $incomingRequests,
            'outgoingRequests' => $outgoingRequests,
            'mustVerifyEmail'  => $request->user() instanceof MustVerifyEmail,
            'status'           => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user      = $request->user();
        $validated = $request->validated();

        // Update user info
        $user->fill([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'],
            'location' => $validated['location'],
            'title'    => $validated['title'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Handle teaching skills
        $user->skills()->where('type', 'teach')->delete();
        if (isset($validated['teachingSkills']) && is_array($validated['teachingSkills'])) {
            foreach ($validated['teachingSkills'] as $skill) {
                if (! empty($skill['name']) && ! empty($skill['category'])) {
                    $user->skills()->create([
                        'name'        => $skill['name'],
                        'category'    => $skill['category'],
                        'type'        => 'teach',
                        'description' => $skill['name'], // Using name as description for now
                    ]);
                }
            }
        }

        // Handle learning skills
        $user->skills()->where('type', 'learn')->delete();
        if (isset($validated['learningSkills']) && is_array($validated['learningSkills'])) {
            foreach ($validated['learningSkills'] as $skill) {
                if (! empty($skill['name']) && ! empty($skill['category'])) {
                    $user->skills()->create([
                        'name'        => $skill['name'],
                        'category'    => $skill['category'],
                        'type'        => 'learn',
                        'description' => $skill['name'], // Using name as description for now
                    ]);
                }
            }
        }

        return back();
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
