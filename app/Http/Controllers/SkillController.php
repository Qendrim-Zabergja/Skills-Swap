<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::with('user')->latest()->get();
        
        return Inertia::render('Skills/Index', [
            'skills' => $skills
        ]);
    }

    public function featured()
    {
        $featuredSkills = Skill::with('user')->inRandomOrder()->take(3)->get();
        
        return response()->json($featuredSkills);
    }

    public function show(Skill $skill)
    {
        $skill->load('user');
        
        return Inertia::render('Skills/Show', [
            'skill' => $skill
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'teachingSkills' => 'array',
            'teachingSkills.*.title' => 'required|string|max:255',
            'teachingSkills.*.description' => 'required|string',
            'teachingSkills.*.category' => 'required|string|max:255',
            'learningSkills' => 'array',
            'learningSkills.*.title' => 'required|string|max:255',
            'learningSkills.*.description' => 'nullable|string',
            'learningSkills.*.category' => 'required|string|max:255',
        ]);

        // Delete existing skills
        $request->user()->skills()->delete();

        // Store teaching skills
        foreach ($request->teachingSkills as $skill) {
            $request->user()->skills()->create([
                'title' => $skill['title'],
                'description' => $skill['description'],
                'category' => $skill['category'],
                'type' => 'teaching'
            ]);
        }

        // Store learning skills
        foreach ($request->learningSkills as $skill) {
            $request->user()->skills()->create([
                'title' => $skill['title'],
                'description' => $skill['description'] ?? '',
                'category' => $skill['category'],
                'type' => 'learning'
            ]);
        }
        
        return redirect()->route('profile.edit')->with('success', 'Skills updated successfully.');
    }

    public function update(Request $request, Skill $skill)
    {
        // $this->authorize('update', $skill);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
        ]);

        $skill->update($request->all());
        
        return redirect()->route('profile.edit')->with('success', 'Skill updated successfully.');
    }

    public function destroy(Skill $skill)
    {
        // $this->authorize('delete', $skill);
        
        $skill->delete();
        
        return redirect()->route('profile.edit')->with('success', 'Skill deleted successfully.');
    }

    public function browse()
    {
        $users = User::with(['skills' => function($q) {
            $q->select('id', 'user_id', 'title', 'description', 'category', 'type');
        }])->get()
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'profile_photo' => $user->profile_photo_url ?? null,
                'location' => $user->location ?? 'Location not specified',
                'rating' => 5, // Placeholder until we implement ratings
                'swaps_completed' => $user->skills->count(),
                'teaching_skills' => $user->skills->where('type', 'teaching')
                    ->map(function($skill) {
                        return [
                            'id' => $skill->id,
                            'name' => $skill->title,
                            'description' => $skill->description,
                            'category' => $skill->category
                        ];
                    })->values(),
                'learning_skills' => $user->skills->where('type', 'learning')
                    ->map(function($skill) {
                        return [
                            'id' => $skill->id,
                            'name' => $skill->title,
                            'description' => $skill->description,
                            'category' => $skill->category
                        ];
                    })->values()
            ];
        });

        return Inertia::render('Skills/Browse', [
            'users' => $users
        ]);
    }

    public function getSkills(Request $request)
    {
        $query = Skill::with('user');

        // Apply search filter
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Apply category filters
        if (!empty($request->categories)) {
            $query->whereIn('category', $request->categories);
        }

        // Get paginated results
        $skills = $query->latest()
            ->paginate(10)
            ->through(function ($skill) {
                return [
                    'id' => $skill->id,
                    'title' => $skill->title,
                    'description' => $skill->description,
                    'category' => $skill->category,
                    'rating' => 4.5, // Placeholder until we implement ratings
                    'user' => [
                        'id' => $skill->user->id,
                        'name' => $skill->user->name,
                        'profile_image' => null // Placeholder until we implement profile photos
                    ]
                ];
            });

        return response()->json($skills);
    }
}