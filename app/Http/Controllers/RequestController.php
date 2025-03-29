<?php

namespace App\Http\Controllers;

use App\Models\Request as SkillRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RequestController extends Controller
{
    public function index()
    {
        $incomingRequests = auth()->user()->receivedRequests()->with(['sender', 'skill'])->latest()->get();
        $outgoingRequests = auth()->user()->sentRequests()->with(['receiver', 'skill'])->latest()->get();
        
        return Inertia::render('Requests/Index', [
            'incomingRequests' => $incomingRequests,
            'outgoingRequests' => $outgoingRequests
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'skill_id' => 'required|exists:skills,id',
            'message' => 'required|string',
        ]);

        $skillRequest = SkillRequest::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'skill_id' => $request->skill_id,
            'message' => $request->message,
            'status' => 'pending',
        ]);
        
        return redirect()->route('requests.index')->with('success', 'Request sent successfully.');
    }

    public function update(Request $request, SkillRequest $skillRequest)
    {
        // $this->authorize('update', $skillRequest);
        
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $skillRequest->update([
            'status' => $request->status,
        ]);
        
        return redirect()->route('requests.index')->with('success', 'Request updated successfully.');
    }
}