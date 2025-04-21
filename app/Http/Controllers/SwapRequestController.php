<?php

namespace App\Http\Controllers;

use App\Models\SwapRequest;
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
                ->with('user')
                ->where('status', 'pending')
                ->get(),
            'outgoingRequests' => $user->sentRequests()
                ->with('recipient')
                ->where('status', 'pending')
                ->get(),
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
        ]);

        $user = Auth::user();
        
        // Check if there's already a pending request
        $existingRequest = SwapRequest::where('user_id', $user->id)
            ->where('recipient_id', $validated['recipient_id'])
            ->where('status', 'pending')
            ->first();
            
        if ($existingRequest) {
            return back()->with('error', 'You already have a pending request with this user.');
        }

        $user->sentRequests()->create($validated);

        return back()->with('success', 'Request sent successfully.');
    }

    /**
     * Accept a swap request.
     */
    public function accept(SwapRequest $request): RedirectResponse
    {
        if ($request->recipient_id !== Auth::id()) {
            abort(403);
        }

        $request->update(['status' => 'accepted']);

        return back()->with('success', 'Request accepted successfully.');
    }

    /**
     * Decline a swap request.
     */
    public function decline(SwapRequest $request): RedirectResponse
    {
        if ($request->recipient_id !== Auth::id()) {
            abort(403);
        }

        $request->update(['status' => 'declined']);

        return back()->with('success', 'Request declined successfully.');
    }

    /**
     * Cancel a swap request.
     */
    public function cancel(SwapRequest $request): RedirectResponse
    {
        if ($request->user_id !== Auth::id()) {
            abort(403);
        }

        $request->update(['status' => 'cancelled']);

        return back()->with('success', 'Request cancelled successfully.');
    }
}
