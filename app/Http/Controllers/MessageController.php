<?php
namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\SkillRequest;
use App\Models\SwapRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

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
                $otherUser   = $lastMessage->user_id === $user->id ? $lastMessage->recipient : $lastMessage->user;

                return [
                    'id'             => $lastMessage->id,
                    'user'           => [
                        'id'                => $otherUser->id,
                        'name'              => $otherUser->name,
                        'profile_photo_url' => $otherUser->profile_photo_url,
                    ],
                    'skill_exchange' => $lastMessage->skill_exchange,
                    'last_message'   => $lastMessage->content,
                    'time'           => $lastMessage->created_at->diffForHumans(),
                    'unread_count'   => $messages->where('recipient_id', $user->id)->whereNull('read_at')->count(),
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
            $query->where(function ($q) use ($currentUser, $user) {
                $q->where('user_id', $currentUser->id)
                    ->where('recipient_id', $user->id);
            })->orWhere(function ($q) use ($currentUser, $user) {
                $q->where('user_id', $user->id)
                    ->where('recipient_id', $currentUser->id);
            });
        })
            ->orderBy('created_at')
            ->get()
            ->map(function ($message) use ($currentUser) {
                return [
                    'id'      => $message->id,
                    'content' => $message->content,
                    'time'    => $message->created_at->format('g:i A'),
                    'date'    => $message->created_at->toDateString(),
                    'is_mine' => $message->user_id === $currentUser->id,
                ];
            });

        // Get the skill exchange context if it exists
        $skillExchange = null;
        $request       = SkillRequest::where(function ($query) use ($currentUser, $user) {
            $query->where(function ($q) use ($currentUser, $user) {
                $q->where('sender_id', $currentUser->id)
                    ->where('recipient_id', $user->id);
            })->orWhere(function ($q) use ($currentUser, $user) {
                $q->where('sender_id', $user->id)
                    ->where('recipient_id', $currentUser->id);
            });
        })
            ->where('status', 'accepted')
            ->with('skill')
            ->first();

        if ($request) {
            $skillExchange = "Skill Exchange: {$request->skill->name}";
        }

        // Mark messages as read
        Message::where('user_id', $user->id)
            ->where('recipient_id', $currentUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return Inertia::render('Messages/Show', [
            'otherUser'     => [
                'id'                => $user->id,
                'name'              => $user->name,
                'profile_photo_url' => $user->profile_photo_url,
            ],
            'messages'      => $messages,
            'skillExchange' => $skillExchange,
            'requestId'     => $request ? $request->id : null,
            'auth'          => [
                'user' => [
                    'id'   => $currentUser->id,
                    'name' => $currentUser->name,
                ],
            ],
        ]);
    }

    /**
     * Store a new message.
     */
    public function store(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'content'        => ['required', 'string'],
            'skill_exchange' => ['nullable', 'string'],
        ]);

        $currentUser = Auth::user();

        $message = Message::create([
            'user_id'        => $currentUser->id,
            'recipient_id'   => $user->id,
            'content'        => $validated['content'],
            'skill_exchange' => $validated['skill_exchange'] ?? null,
        ]);

        // Broadcast the message
        try {
            Log::info('Broadcasting message to channels', [
                'message'      => $message->toArray(),
                'sender_id'    => $currentUser->id,
                'recipient_id' => $user->id,
            ]);

            // Broadcast to all channels (don't use toOthers() as we want to ensure delivery)
            broadcast(new MessageSent($message));
            
            Log::info('Message broadcast completed');
        } catch (\Exception $e) {
            Log::error('Broadcasting failed: ' . $e->getMessage(), [
                'exception' => $e,
                'message'   => $message->toArray(),
            ]);
        }

        return back();
    }

    // Add this method to your MessageController
    public function conversations()
    {
        try {
            $user = Auth::user();

            $conversations = Message::where('user_id', $user->id)
                ->orWhere('recipient_id', $user->id)
                ->with(['user', 'recipient'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy(function ($message) use ($user) {
                    return $message->user_id === $user->id ? $message->recipient_id : $message->user_id;
                })
                ->map(function ($messages) use ($user) {
                    $lastMessage = $messages->first();
                    $otherUser   = $lastMessage->user_id === $user->id ? $lastMessage->recipient : $lastMessage->user;

                // Get the skill exchange context from the associated swap request if it exists
                $swapRequest = SwapRequest::where(function ($query) use ($user, $otherUser) {
                    $query->where('user_id', $user->id)
                        ->where('recipient_id', $otherUser->id);
                })
                    ->orWhere(function ($query) use ($user, $otherUser) {
                        $query->where('user_id', $otherUser->id)
                            ->where('recipient_id', $user->id);
                    })
                    ->where('status', 'Accepted')
                    ->first();

                // Get the skill names based on who is the sender
                $skillWanted = '';
                $skillOffered = '';
                if ($swapRequest) {
                    if ($swapRequest->user_id === $user->id) {
                        $skillWanted = $swapRequest->skill_wanted;
                        $skillOffered = $swapRequest->skill_offered;
                    } else {
                        $skillWanted = $swapRequest->skill_offered;
                        $skillOffered = $swapRequest->skill_wanted;
                    }
                }

                return [
                    'id'            => $lastMessage->id,
                    'other_user'    => [
                        'id'   => $otherUser->id,
                        'name' => $otherUser->name,
                    ],
                    'skill_offered' => $skillOffered,
                    'skill_wanted'  => $skillWanted,
                    'last_message'  => [
                        'content'    => $lastMessage->content,
                        'created_at' => $lastMessage->created_at,
                        'is_read'    => !is_null($lastMessage->read_at),
                        'user_id'    => $lastMessage->user_id,
                    ],
                ];
            })
            ->values()
            ->toArray();

            return response()->json($conversations);
        } catch (\Exception $e) {
            \Log::error('Error in conversations method: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id()
            ]);
            return response()->json(['error' => 'An error occurred while fetching conversations'], 500);
        }
    }

    /**
     * Mark a specific conversation as read
     */
    public function markAsRead(Request $request, $conversation)
    {
        $user = Auth::user();
        
        // Mark the message as read
        Message::where('id', $conversation)
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        return response()->json(['success' => true]);
    }
    
    /**
     * Mark all messages in a conversation with a specific user as read
     */
    public function markConversationAsRead(Request $request, User $user)
    {
        $currentUser = Auth::user();
        
        // Mark all messages from the other user as read
        $updated = Message::where('user_id', $user->id)
            ->where('recipient_id', $currentUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        return response()->json([
            'success' => true,
            'count' => $updated
        ]);
    }
}
