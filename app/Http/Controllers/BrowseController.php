<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Skill;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BrowseController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['skills' => function ($query) {
            $query->select('id', 'user_id', 'name', 'type');
        }]);

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('skills', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Apply category filter
        if ($request->has('categories') && !empty($request->categories)) {
            $categories = $request->categories;
            $query->whereHas('skills', function ($q) use ($categories) {
                $q->whereIn('category', $categories);
            });
        }

        // Get paginated results
        $users = $query->paginate(10)->through(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'location' => $user->location,
                'rating' => 5, // Placeholder for now
                'swaps_completed' => 3, // Placeholder for now
                'teaching_skills' => $user->skills->where('type', 'teaching')->values(),
                'learning_skills' => $user->skills->where('type', 'learning')->values(),
            ];
        });

        return Inertia::render('BrowseSkills', [
            'skills' => $users->items(),
            'filters' => [
                'search' => $request->search,
                'categories' => $request->categories,
                'rating' => $request->rating,
                'lookingForMySkills' => $request->lookingForMySkills,
                'offeringSkillsIWant' => $request->offeringSkillsIWant,
                'perfectMatchesOnly' => $request->perfectMatchesOnly,
            ],
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        ]);
    }
}
