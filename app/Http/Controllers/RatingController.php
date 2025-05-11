<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\SwapRequest;
use Illuminate\Http\Request as HttpRequest;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class RatingController extends Controller
{
    public function store(HttpRequest $request, SwapRequest $swapRequest)
    {
        try {
            $validated = $request->validate([
                'score' => 'required|integer|between:1,5',
                'comment' => 'nullable|string|max:500',
            ]);

            // Check if user is part of the request
            $userId = auth()->id();
            if ($swapRequest->user_id !== $userId && $swapRequest->recipient_id !== $userId) {
                return back()->with('error', 'You are not authorized to rate this request.');
            }

            // Check if the request exists
            if (!$swapRequest->exists) {
                return back()->with('error', 'Swap request not found.');
            }

            // Determine who is being rated
            $ratedUserId = $swapRequest->user_id === $userId 
                ? $swapRequest->recipient_id 
                : $swapRequest->user_id;

            // Check for existing rating
            $existingRating = Rating::where([
                'request_id' => $swapRequest->id,
                'rater_id' => $userId,
            ])->first();

            if ($existingRating) {
                return back()->with('error', 'You have already rated this request.');
            }

            $rating = Rating::create([
                'request_id' => $swapRequest->id,
                'rater_id' => $userId,
                'rated_user_id' => $ratedUserId,
                'score' => $validated['score'],
                'comment' => $validated['comment'] ?? null,
            ]);

            if (!$rating) {
                throw new \Exception('Failed to create rating record');
            }

            return back()->with('success', 'Rating submitted successfully!');

        } catch (\Exception $e) {
            Log::error('Rating creation failed: ' . $e->getMessage(), [
                'request_id' => $swapRequest->id,
                'user_id' => $userId ?? null,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to submit rating. Please try again.');
        }
    }

    public function checkStatus(SwapRequest $swapRequest)
    {
        $userId = auth()->id();
        $rating = Rating::where([
            'request_id' => $swapRequest->id,
            'rater_id' => $userId,
        ])->first();

        return response()->json([
            'hasRated' => (bool) $rating,
            'rating' => $rating,
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
            'ratings' => $ratings,
            'averageRating' => round($averageRating, 1),
        ]);
    }
}
