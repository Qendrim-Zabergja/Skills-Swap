<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\Message;
use Illuminate\Http\Request; // Now you can use Laravel's Request without conflict
use App\Models\SkillRequest; // Great! You've renamed the model
use Inertia\Inertia;

class MessageController extends Controller
{
    public function index(Request $request, SkillRequest $skillRequest)
    {
        $skillRequest->load(['sender', 'receiver', 'skill']);
        $messages = $skillRequest->messages()->with(['sender'])->orderBy('created_at')->get();
        
        // Mark messages as read
        $skillRequest->messages()
            ->where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return Inertia::render('Messages/Index', [
            'request' => $skillRequest,
            'messages' => $messages
        ]);
    } // This closing brace was missing in your code

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