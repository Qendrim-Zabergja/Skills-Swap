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
            $query->select('id', 'user_id', 'name', 'description', 'type', 'category');
        }]);

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('skills', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Apply category filter
        if ($request->has('category') && $request->category) {
            $category = $request->category;
            $query->whereHas('skills', function ($q) use ($category) {
                $q->where('category', $category);
            });
        } elseif ($request->has('categories') && !empty($request->categories)) {
            $categories = is_array($request->categories) ? $request->categories : [$request->categories];
            $query->whereHas('skills', function ($q) use ($categories) {
                $q->whereIn('category', $categories);
            });
        }

        // Get paginated results
        $users = $query->paginate(10)->through(function ($user) use ($request) {
            $skills = $user->skills;
            
            // If there's a search query, prioritize matching skills
            if ($request->search) {
                $search = strtolower($request->search);
                $skills = $skills->sortBy(function ($skill) use ($search) {
                    // Calculate relevance score
                    $nameMatch = str_contains(strtolower($skill->name), $search) ? 2 : 0;
                    $descMatch = str_contains(strtolower($skill->description), $search) ? 1 : 0;
                    $categoryMatch = str_contains(strtolower($skill->category), $search) ? 1 : 0;
                    return -($nameMatch + $descMatch + $categoryMatch); // Negative for desc sort
                });
            }

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'location' => $user->location ?? 'Not specified',
                'avatar' => $user->avatar,
                'rating' => $user->rating ?? 4.5,
                'swaps_completed' => $user->swaps_completed ?? rand(0, 30),
                'teaching_skills' => $skills->where('type', 'teaching')
                    ->values()
                    ->map(function ($skill) {
                        return [
                            'id' => $skill->id,
                            'name' => $skill->name,
                            'description' => $skill->description,
                            'category' => $skill->category
                        ];
                    }),
                'learning_skills' => $skills->where('type', 'learning')
                    ->values()
                    ->map(function ($skill) {
                        return [
                            'id' => $skill->id,
                            'name' => $skill->name,
                            'description' => $skill->description,
                            'category' => $skill->category
                        ];
                    })
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
            'initialSearch' => $request->search,
            'initialCategory' => $request->category,
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
            'auth' => [
                'user' => auth()->user()
            ]
        ]);
    }
}
