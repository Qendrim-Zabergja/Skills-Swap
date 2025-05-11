<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Request as SkillRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RatingController extends Controller
{
    public function store(Request $request, SkillRequest $skillRequest)
    {
        $validated = $request->validate([
            'score' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500',
        ]);

        // Check if user is part of the request
        $userId = auth()->id();
        if ($skillRequest->sender_id !== $userId && $skillRequest->receiver_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Determine who is being rated
        $ratedUserId = $skillRequest->sender_id === $userId 
            ? $skillRequest->receiver_id 
            : $skillRequest->sender_id;

        // Check for existing rating
        $existingRating = Rating::where([
            'request_id' => $skillRequest->id,
            'rater_id' => $userId,
        ])->first();

        if ($existingRating) {
            return response()->json(['message' => 'Already rated'], 400);
        }

        $rating = Rating::create([
            'request_id' => $skillRequest->id,
            'rater_id' => $userId,
            'rated_user_id' => $ratedUserId,
            'score' => $validated['score'],
            'comment' => $validated['comment'],
        ]);

        return response()->json($rating);
    }

    public function checkStatus(SkillRequest $skillRequest)
    {
        $userId = auth()->id();
        $rating = Rating::where([
            'request_id' => $skillRequest->id,
            'rater_id' => $userId,
        ])->first();

        return response()->json([
            'hasRated' => (bool) $rating,
            'rating' => $rating,
        ]);
    }

    public function userRatings(Request $request, $userId)
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
