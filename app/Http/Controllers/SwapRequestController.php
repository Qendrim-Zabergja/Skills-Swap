<?php
namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\SwapRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

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
                    $ratings = $request->ratings->map(function ($rating) {
                        return [
                            'id'            => $rating->id,
                            'rater_id'      => $rating->rater_id,
                            'rated_user_id' => $rating->rated_user_id,
                            'score'         => $rating->score,
                            'comment'       => $rating->comment,
                        ];
                    });
                    $rated = $ratings->where('rater_id', $user->id)->isNotEmpty();
                    return [
                        'id'            => $request->id,
                        'user'          => [
                            'id'                => $request->user->id,
                            'name'              => $request->user->name,
                            'profile_photo_url' => $request->user->profile_photo_url,
                        ],
                        'skill_wanted'  => $request->skill_wanted,
                        'skill_offered' => $request->skill_offered,
                        'message'       => $request->message,
                        'status'        => $request->status,
                        'created_at'    => $request->created_at,
                        'ratings'       => $ratings,
                        'rated'         => $rated,
                    ];
                }),
            'outgoingRequests' => $user->sentRequests()
                ->with(['recipient', 'skill', 'ratings'])
                ->latest()
                ->get()
                ->map(function ($request) use ($user) {
                    $ratings = $request->ratings->map(function ($rating) {
                        return [
                            'id'            => $rating->id,
                            'rater_id'      => $rating->rater_id,
                            'rated_user_id' => $rating->rated_user_id,
                            'score'         => $rating->score,
                            'comment'       => $rating->comment,
                        ];
                    });
                    $rated = $ratings->where('rater_id', $user->id)->isNotEmpty();
                    return [
                        'id'            => $request->id,
                        'recipient'     => [
                            'id'                => $request->recipient->id,
                            'name'              => $request->recipient->name,
                            'profile_photo_url' => $request->recipient->profile_photo_url,
                        ],
                        'skill_wanted'  => $request->skill_wanted,
                        'skill_offered' => $request->skill_offered,
                        'message'       => $request->message,
                        'status'        => $request->status,
                        'created_at'    => $request->created_at,
                        'ratings'       => $ratings,
                        'rated'         => $rated,
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
            'recipient_id'  => ['required', 'exists:users,id'],
            'skill_wanted'  => ['required', 'string'],
            'skill_offered' => ['required', 'string'],
            'message'       => ['required', 'string'],
            'availability'  => ['nullable', 'string'],
            'duration'      => ['nullable', 'integer'],
        ]);

        $swapRequest = SwapRequest::create([
            'user_id'       => auth()->id(),
            'recipient_id'  => $validated['recipient_id'],
            'skill_wanted'  => $validated['skill_wanted'],
            'skill_offered' => $validated['skill_offered'],
            'message'       => $validated['message'],
            'availability'  => $validated['availability'],
            'duration'      => $validated['duration'],
            'status'        => 'Pending',
        ]);

        // Create a chat message with the request details
        $currentUser = Auth::user();
        $recipient   = User::find($validated['recipient_id']);

        // Format the message content including availability and duration
        $messageContent = "Skill Request: I would like to learn {$validated['skill_wanted']} and can offer {$validated['skill_offered']} in exchange.\n";
        $messageContent .= "Message: {$validated['message']}\n";

        if (! empty($validated['availability'])) {
            $messageContent .= "Availability: {$validated['availability']}\n";
        }

        if (! empty($validated['duration'])) {
            $messageContent .= "Session Duration: {$validated['duration']} minutes\n";
        }

        $message = Message::create([
            'user_id'        => $currentUser->id,
            'recipient_id'   => $recipient->id,
            'content'        => $messageContent,
            'skill_exchange' => "{$validated['skill_offered']} for {$validated['skill_wanted']}",
        ]);

        // Broadcast the message
        try {
            broadcast(new MessageSent($message));
        } catch (\Exception $e) {
            Log::error('Broadcasting failed: ' . $e->getMessage(), [
                'exception' => $e,
                'message'   => $message->toArray(),
            ]);
        }

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
            'status' => 'Accepted',
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
            'status' => 'Declined',
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
            'status' => 'Cancelled',
        ]);

        return back()->with('success', 'Request cancelled successfully.');
    }

    /**
     * Cancel a swap request via API (sender or recipient).
     */
    public function apiCancel(Request $request, SwapRequest $swapRequest)
    {
        if ($swapRequest->user_id !== auth()->id() && $swapRequest->recipient_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        if (! in_array($swapRequest->status, ['Pending', 'Accepted'])) {
            return response()->json(['success' => false, 'message' => 'This request cannot be cancelled.'], 400);
        }
        $swapRequest->update(['status' => 'Cancelled']);
        return response()->json(['success' => true]);
    }

    /**
     * Hard delete a swap request via API (sender or recipient).
     */
    public function destroy(Request $request, SwapRequest $swapRequest)
    {
        if ($swapRequest->user_id !== auth()->id() && $swapRequest->recipient_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $swapRequest->delete();
        return response()->json(['success' => true]);
    }
}
