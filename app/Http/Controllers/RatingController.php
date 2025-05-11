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
        $validated = $request->validate([
            'score' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500',
        ]);

        // Check if user is part of the request
        $userId = auth()->id();
        if ($swapRequest->sender_id !== $userId && $swapRequest->receiver_id !== $userId) {
            return back()->with('error', 'You are not authorized to rate this request.');
        }

        // Determine who is being rated
        $ratedUserId = $swapRequest->sender_id === $userId 
            ? $swapRequest->receiver_id 
            : $swapRequest->sender_id;

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
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Rating submitted successfully!');
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
