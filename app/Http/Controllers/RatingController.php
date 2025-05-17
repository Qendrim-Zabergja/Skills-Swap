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
                return response()->json(['error' => 'You are not authorized to rate this request.'], 403);
            }

            if (!$swapRequest->exists()) {
                return response()->json(['error' => 'Swap request not found.'], 404);
            }

            $ratedUserId = $swapRequest->user_id === $userId
                ? $swapRequest->recipient_id
                : $swapRequest->user_id;

            $existingRating = Rating::where([
                'rater_id'      => $userId,
                'rated_user_id' => $ratedUserId,
            ])->first();

            if ($existingRating) {
                return response()->json(['error' => 'You have already rated this user.'], 409);
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

            return response()->json([
                'success'       => true,
                'message'       => 'Rating submitted successfully!',
                'rated_user_id' => $ratedUserId,
            ]);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['errors' => $ve->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Rating creation failed: ' . $e->getMessage(), [
                'request_id' => $swapRequest->getKey(),
                'user_id'    => $userId ?? null,
                'error'      => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors([
                'rating' => 'Failed to submit rating. Please try again.',
            ]);

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
