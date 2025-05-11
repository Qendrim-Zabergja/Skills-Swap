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
        }, 'receivedRatings'])->select(['id', 'name', 'email', 'location', 'profile_photo_path', 'created_at']);

        // Apply category filter first (if present)
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

        // Then apply search filter if present
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('skills', function ($q) use ($search) {
                $q->where(function ($subquery) use ($search) {
                    $subquery->where('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                });
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
                    return -($nameMatch + $descMatch); // Negative for desc sort
                });
            }

            // If category filter is applied, only show skills from that category
            if ($request->category) {
                $skills = $skills->filter(function ($skill) use ($request) {
                    return $skill->category === $request->category;
                });
            }

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'profile_photo_url' => $user->profile_photo_url,
                'location' => $user->location ?? 'Not specified',
                'average_rating' => $user->average_rating,
                'total_ratings' => $user->receivedRatings->count(),
                'swaps_completed' => $user->swaps_completed ?? rand(0, 30)
            ];
            
            // Get teaching skills
            $userData['teaching_skills'] = $skills->where('type', 'teaching')
                ->where('user_id', $user->id)
                ->map(function($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'description' => $skill->description,
                        'category' => $skill->category
                    ];
                })->values()->all();
            
            // Get learning skills
            $userData['learning_skills'] = $skills->where('type', 'learning')
                ->where('user_id', $user->id)
                ->map(function($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'description' => $skill->description,
                        'category' => $skill->category
                    ];
                })->values()->all();
                
            return $userData;
        });

        // Debug user data
        $items = $users->items();
        info('Total users found:', ['count' => count($items)]);
        
        if (!empty($items)) {
            info('First user data:', [
                'raw' => $items[0],
                'skills' => $items[0]['teaching_skills'] ?? [],
                'profile' => [
                    'name' => $items[0]['name'] ?? 'no name',
                    'photo_url' => $items[0]['profile_photo_url'] ?? 'no url',
                    'photo_path' => $items[0]['profile_photo_path'] ?? 'no path'
                ]
            ]);
        } else {
            info('No users found in the database');
        }

        // Also check the query being executed
        info('SQL Query:', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        $usersData = $users->items();
        
        // Simple debug log
        info('Debug data', [
            'users_count' => count($usersData),
            'first_user' => !empty($usersData) ? $usersData[0] : null
        ]);

        return Inertia::render('BrowseSkills', [
            'skills' => $usersData,
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
