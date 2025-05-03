<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Response;
use Inertia\Inertia;

class MessageController extends Controller
{
    /**
     * Display a listing of the messages.
     */
    public function index(): Response
    {
        $user = Auth::user();
        
        $conversations = Message::where('user_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) use ($user) {
                return $message->user_id === $user->id ? $message->recipient_id : $message->user_id;
            })
            ->map(function ($messages) use ($user) {
                $lastMessage = $messages->first();
                $otherUser = $lastMessage->user_id === $user->id ? $lastMessage->recipient : $lastMessage->user;
                
                return [
                    'id' => $lastMessage->id,
                    'user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'profile_photo_url' => $otherUser->profile_photo_url,
                    ],
                    'skill_exchange' => $lastMessage->skill_exchange,
                    'last_message' => $lastMessage->content,
                    'time' => $lastMessage->created_at->diffForHumans(),
                    'unread_count' => $messages->where('recipient_id', $user->id)->whereNull('read_at')->count(),
                ];
            })
            ->values();

        return Inertia::render('Messages/Index', [
            'conversations' => $conversations,
        ]);
    }

    /**
     * Display a conversation with a specific user.
     */
    public function show(User $user): Response
    {
        $currentUser = Auth::user();
        
        $messages = Message::where(function ($query) use ($currentUser, $user) {
                $query->where('user_id', $currentUser->id)
                    ->where('recipient_id', $user->id);
            })
            ->orWhere(function ($query) use ($currentUser, $user) {
                $query->where('user_id', $user->id)
                    ->where('recipient_id', $currentUser->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();
            
        // Mark messages as read
        Message::where('user_id', $user->id)
            ->where('recipient_id', $currentUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return Inertia::render('Messages/Show', [
            'otherUser' => [
                'id' => $user->id,
                'name' => $user->name,
                'profile_photo_url' => $user->profile_photo_url,
            ],
            'messages' => $messages->map(function ($message) use ($currentUser) {
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'time' => $message->created_at->format('g:i A'),
                    'date' => $message->created_at->format('M j, Y'),
                    'is_mine' => $message->user_id === $currentUser->id,
                ];
            }),
            'auth' => [
                'user' => [
                    'id' => $currentUser->id,
                    'name' => $currentUser->name,
                ]
            ]
        ]);
    }

    /**
     * Store a new message.
     */
    public function store(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string'],
            'skill_exchange' => ['nullable', 'string'],
        ]);

        $currentUser = Auth::user();
        
        $message = Message::create([
            'user_id' => $currentUser->id,
            'recipient_id' => $user->id,
            'content' => $validated['content'],
            'skill_exchange' => $validated['skill_exchange'] ?? null,
        ]);

        // Broadcast the message
        try {
            Log::info('Broadcasting message to channel chat.' . $user->id, [
                'message' => $message->toArray(),
                'sender_id' => $currentUser->id,
                'recipient_id' => $user->id
            ]);
            
            broadcast(new MessageSent($message))->toOthers();
            
            Log::info('Message broadcast completed');
        } catch (\Exception $e) {
            Log::error('Broadcasting failed: ' . $e->getMessage(), [
                'exception' => $e,
                'message' => $message->toArray()
            ]);
        }

        return back();
    }
}