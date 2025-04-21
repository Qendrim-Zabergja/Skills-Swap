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
            'teachingSkills.*.name' => 'required|string|max:255',
            'teachingSkills.*.description' => 'required|string',
            'learningSkills' => 'array',
            'learningSkills.*.name' => 'required|string|max:255',
            'learningSkills.*.description' => 'nullable|string',
        ]);

        // Delete existing skills
        $request->user()->skills()->delete();

        // Store teaching skills
        foreach ($request->teachingSkills as $skill) {
            $request->user()->skills()->create([
                'name' => $skill['name'],
                'description' => $skill['description'],
                'type' => 'teach'
            ]);
        }

        // Store learning skills
        foreach ($request->learningSkills as $skill) {
            $request->user()->skills()->create([
                'name' => $skill['name'],
                'description' => $skill['description'] ?? '',
                'type' => 'learn'
            ]);
        }
        
        return redirect()->route('profile.edit')->with('success', 'Skills updated successfully.');
    }

    public function update(Request $request, Skill $skill)
    {
        // $this->authorize('update', $skill);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:teach,learn'
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
            $q->select('id', 'user_id', 'name', 'description', 'type');
        }])->get()
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'surname' => $user->surname,
                'profile_photo' => $user->profile_photo_url ?? asset('images/default-avatar.png'),
                'location' => $user->location ?? 'Location not specified',
                'rating' => 5, // Placeholder until we implement ratings
                'swaps_completed' => $user->skills->count(),
                'teaching_skills' => $user->skills->where('type', 'teach')
                    ->map(function($skill) {
                        return [
                            'id' => $skill->id,
                            'name' => $skill->name,
                            'description' => $skill->description
                        ];
                    })->values(),
                'learning_skills' => $user->skills->where('type', 'learn')
                    ->map(function($skill) {
                        return [
                            'id' => $skill->id,
                            'name' => $skill->name,
                            'description' => $skill->description
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
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Get paginated results
        $skills = $query->latest()
            ->paginate(10)
            ->through(function ($skill) {
                return [
                    'id' => $skill->id,
                    'name' => $skill->name,
                    'description' => $skill->description,
                    'type' => $skill->type,
                    'rating' => 4.5, // Placeholder until we implement ratings
                    'user' => [
                        'id' => $skill->user->id,
                        'name' => $skill->user->name,
                        'profile_photo_url' => $skill->user->profile_photo_url
                    ]
                ];
            });

        return response()->json($skills);
    }
}