<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        // Fetch top 3 highest-rated users with their completed swaps count
        $featuredUsers = User::with(['teachingSkills', 'learningSkills', 'receivedRatings'])
            ->whereHas('teachingSkills') // Only users who have teaching skills
            ->withCount(['receivedRatings as ratings_count'])
            ->withCount(['sentRequests as completed_swaps' => function($query) {
                $query->where('status', 'Accepted');
            }])
            ->selectRaw('users.*, COALESCE((SELECT AVG(score) FROM ratings WHERE rated_user_id = users.id), 5) as avg_rating')
            ->orderBy('avg_rating', 'DESC')
            ->limit(3)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name ?? ($user->first_name . ' ' . $user->last_name),
                    'location' => $user->location,
                    'profile_photo_url' => $user->profile_photo_url,
                    'rating' => $user->average_rating ?? 5,
                    'swaps_completed' => $user->completed_swaps,
                    'teaching_skills' => $user->teachingSkills->map(function($skill) {
                        return [
                            'id' => $skill->id,
                            'name' => $skill->name,
                            'category' => $skill->category,
                        ];
                    }),
                    'learning_skills' => $user->learningSkills->map(function($skill) {
                        return [
                            'id' => $skill->id,
                            'name' => $skill->name,
                            'category' => $skill->category,
                        ];
                    }),
                ];
            });

        return Inertia::render('Welcome', [
            'featuredUsers' => $featuredUsers,
            'canLogin' => \Route::has('login'),
            'canRegister' => \Route::has('register'),
        ]);
    }
}