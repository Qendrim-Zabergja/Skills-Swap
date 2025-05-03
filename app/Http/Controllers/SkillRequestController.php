<?php

namespace App\Http\Controllers;

use App\Models\SkillRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class SkillRequestController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'learning_skill' => 'required|string',
            'teaching_skill' => 'required|string',
            'message' => 'required|string',
            'availability' => 'required|string',
            'duration' => 'required|string',
            'recipient_id' => 'required|exists:users,id'
        ]);

        $skillRequest = SkillRequest::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $validated['recipient_id'],
            'learning_skill' => $validated['learning_skill'],
            'teaching_skill' => $validated['teaching_skill'],
            'message' => $validated['message'],
            'availability' => $validated['availability'],
            'duration' => $validated['duration'],
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Skill exchange request sent successfully!');
    }

    public function accept(SkillRequest $request)
    {
        $this->authorize('accept', $request);
        
        $request->update(['status' => 'accepted']);
        
        return redirect()->back()->with('success', 'Skill exchange request accepted!');
    }

    public function decline(SkillRequest $request)
    {
        $this->authorize('decline', $request);
        
        $request->update(['status' => 'declined']);
        
        return redirect()->back()->with('success', 'Skill exchange request declined!');
    }

    public function cancel(SkillRequest $request)
    {
        $this->authorize('cancel', $request);
        
        $request->update(['status' => 'cancelled']);
        
        return redirect()->back()->with('success', 'Skill exchange request cancelled!');
    }
}
