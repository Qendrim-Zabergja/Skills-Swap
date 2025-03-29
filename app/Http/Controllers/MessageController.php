<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\Message;
use App\Models\Request as SkillRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageController extends Controller
{
    public function index(SkillRequest $request)
    {
        // $this->authorize('view', $request);
        
        $request->load(['sender', 'receiver', 'skill']);
        $messages = $request->messages()->with(['sender'])->orderBy('created_at')->get();
        
        // Mark messages as read
        $request->messages()
            ->where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return Inertia::render('Messages/Index', [
            'request' => $request,
            'messages' => $messages
        ]);
    }

    public function store(Request $request, SkillRequest $skillRequest)
    {
        // $this->authorize('message', $skillRequest);
        
        $request->validate([
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'request_id' => $skillRequest->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $skillRequest->sender_id == auth()->id() 
                ? $skillRequest->receiver_id 
                : $skillRequest->sender_id,
            'content' => $request->content,
        ]);
        
        $message->load('sender');
        
        broadcast(new NewMessage($message))->toOthers();
        
        return back();
    }
}