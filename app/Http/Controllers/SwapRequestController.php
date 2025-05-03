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
        $request->validate([
            'recipient_id' => ['required', 'exists:users,id'],
            'skill_wanted' => ['required', 'string'],
            'skill_offered' => ['required', 'string'],
            'message' => ['nullable', 'string'],
            'availability' => ['nullable', 'string'],
            'duration' => ['nullable', 'integer']
        ]);

        $swapRequest = SwapRequest::create([
            'user_id' => auth()->id(),
            'recipient_id' => $request->recipient_id,
            'skill_wanted' => $request->skill_wanted,
            'skill_offered' => $request->skill_offered,
            'message' => $request->message,
            'availability' => $request->availability,
            'duration' => $request->duration,
            'status' => 'Pending'
        ]);

        return back()->with('success', 'Request sent successfully.');
    }

    /**
     * Accept a swap request.
     */
    public function accept(SwapRequest $swapRequest): RedirectResponse
    {
        if ($swapRequest->recipient_id !== Auth::id()) {
            abort(403);
        }

        $swapRequest->update([
            'status' => 'Accepted'
        ]);

        return back()->with('success', 'Request accepted successfully.');
    }

    /**
     * Decline a swap request.
     */
    public function decline(SwapRequest $swapRequest): RedirectResponse
    {
        if ($swapRequest->recipient_id !== Auth::id()) {
            abort(403);
        }

        $swapRequest->update([
            'status' => 'Declined'
        ]);

        return back()->with('success', 'Request declined successfully.');
    }

    /**
     * Cancel a swap request.
     */
    public function cancel(SwapRequest $swapRequest): RedirectResponse
    {
        if ($swapRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $swapRequest->update([
            'status' => 'Cancelled'
        ]);

        return back();
    }
}
