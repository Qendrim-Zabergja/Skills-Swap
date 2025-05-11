<?php

namespace App\Http\Controllers;

use App\Models\SwapRequest;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Inertia\Inertia;

class SwapRequestController extends Controller
{
    /**
     * Display a listing of the requests.
     */
    public function index(): Response
    {
        $user = Auth::user();
        
        return Inertia::render('Requests/Index', [
            'incomingRequests' => $user->receivedRequests()
                ->with(['user', 'skill', 'ratings'])
                ->latest()
                ->get()
                ->map(function ($request) use ($user) {
                    $rated = $request->ratings()
                        ->where('rater_id', $user->id)
                        ->exists();
                    
                    return [
                        'id' => $request->id,
                        'user' => [
                            'id' => $request->user->id,
                            'name' => $request->user->name,
                            'profile_photo_url' => $request->user->profile_photo_url,
                        ],
                        'skill_wanted' => $request->skill_wanted,
                        'skill_offered' => $request->skill_offered,
                        'message' => $request->message,
                        'status' => $request->status,
                        'created_at' => $request->created_at,
                        'rated' => $rated,
                    ];
                }),
            'outgoingRequests' => $user->sentRequests()
                ->with(['recipient', 'skill', 'rating'])
                ->latest()
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
                        'message' => $request->message,
                        'status' => $request->status,
                        'created_at' => $request->created_at,
                    ];
                }),
        ]);
    }

    /**
     * Store a newly created request.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_id' => ['required', 'exists:users,id'],
            'skill_wanted' => ['required', 'string'],
            'skill_offered' => ['required', 'string'],
            'message' => ['required', 'string'],
            'availability' => ['nullable', 'string'],
            'duration' => ['nullable', 'integer']
        ]);

        $swapRequest = SwapRequest::create([
            'user_id' => auth()->id(),
            'recipient_id' => $validated['recipient_id'],
            'skill_wanted' => $validated['skill_wanted'],
            'skill_offered' => $validated['skill_offered'],
            'message' => $validated['message'],
            'availability' => $validated['availability'],
            'duration' => $validated['duration'],
            'status' => 'Pending'
        ]);

        return back()->with('success', 'Request sent successfully! Waiting for response.');
    }

    /**
     * Accept a swap request.
     */
    public function accept(SwapRequest $swapRequest): RedirectResponse
    {
        if ($swapRequest->recipient_id !== Auth::id()) {
            abort(403, 'You are not authorized to accept this request.');
        }

        if ($swapRequest->status !== 'Pending') {
            return back()->with('error', 'This request cannot be accepted anymore.');
        }

        $swapRequest->update([
            'status' => 'Accepted'
        ]);

        // Create a message thread or enable messaging here if needed

        return back()->with('success', 'Request accepted! You can now message each other.');
    }

    /**
     * Decline a swap request.
     */
    public function decline(SwapRequest $swapRequest): RedirectResponse
    {
        if ($swapRequest->recipient_id !== Auth::id()) {
            abort(403, 'You are not authorized to decline this request.');
        }

        if ($swapRequest->status !== 'Pending') {
            return back()->with('error', 'This request cannot be declined anymore.');
        }

        $swapRequest->update([
            'status' => 'Declined'
        ]);

        return back()->with('success', 'Request declined.');
    }

    /**
     * Cancel a swap request.
     */
    public function cancel(SwapRequest $swapRequest): RedirectResponse
    {
        if ($swapRequest->user_id !== auth()->id()) {
            abort(403, 'You are not authorized to cancel this request.');
        }

        if ($swapRequest->status !== 'Pending') {
            return back()->with('error', 'This request cannot be cancelled anymore.');
        }

        $swapRequest->update([
            'status' => 'Cancelled'
        ]);

        return back()->with('success', 'Request cancelled successfully.');
    }
}
