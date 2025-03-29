<?php

namespace App\Http\Controllers;

use App\Models\Skill;
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
        ]);

        $skill = auth()->user()->skills()->create($request->all());
        
        return redirect()->route('profile.edit')->with('success', 'Skill created successfully.');
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
}