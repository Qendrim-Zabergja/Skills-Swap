<?php
namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\SwapRequest;
use Illuminate\Http\Request as HttpRequest;
use Inertia\Inertia;

class RatingController extends Controller
{
    public function store(HttpRequest $request, SwapRequest $swapRequest)
    {
        try {
            $validated = $request->validate([
                'score'   => 'required|integer|between:1,5',
                'comment' => 'nullable|string|max:500',
            ]);

            $userId = auth()->id();

            // Authorization check
            if ($swapRequest->user_id !== $userId && $swapRequest->recipient_id !== $userId) {
                return back()->with('error', 'You are not authorized to rate this request.');
            }

            if (!$swapRequest->exists()) {
                return back()->with('error', 'Swap request not found.');
            }

            $ratedUserId = $swapRequest->user_id === $userId
                ? $swapRequest->recipient_id
                : $swapRequest->user_id;

            $existingRating = Rating::where([
                'rater_id'      => $userId,
                'rated_user_id' => $ratedUserId,
            ])->first();

            if ($existingRating) {
                return back()->with('error', 'You have already rated this user.');
            }

            $rating = Rating::create([
                'request_id'    => $swapRequest->getKey(),
                'rater_id'      => $userId,
                'rated_user_id' => $ratedUserId,
                'score'         => $validated['score'],
                'comment'       => $validated['comment'] ?? null,
            ]);

            if (! $rating) {
                throw new \Exception('Failed to create rating record');
            }

            cache()->tags(['user-ratings'])->forget('user-' . $ratedUserId . '-ratings');

            return back()->with('success', 'Rating submitted successfully!');

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['errors' => $ve->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Rating creation failed: ' . $e->getMessage(), [
                'request_id' => $swapRequest->getKey(),
                'user_id'    => $userId ?? null,
                'error'      => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to submit rating. Please try again.');

        }
    }

    public function checkStatus(SwapRequest $swapRequest)
    {
        $userId = auth()->id();
        $rating = Rating::where([
            'request_id' => $swapRequest->id,
            'rater_id'   => $userId,
        ])->first();

        return response()->json([
            'hasRated' => (bool) $rating,
            'rating'   => $rating,
        ]);
    }

    public function userRatings(HttpRequest $request, $userId)
    {
        $ratings = Rating::with(['rater', 'request'])
            ->where('rated_user_id', $userId)
            ->latest()
            ->paginate(10);

        $averageRating = Rating::where('rated_user_id', $userId)->avg('score');

        return Inertia::render('Ratings/UserRatings', [
            'ratings'       => $ratings,
            'averageRating' => round($averageRating, 1),
        ]);
    }
}
