<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
        Log::info('MessageSent event constructed', ['message' => $message]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info('Broadcasting MessageSent event', [
            'message_id' => $this->message->id,
            'content' => $this->message->content,
            'sender_id' => $this->message->user_id,
            'recipient_id' => $this->message->recipient_id,
            'created_at' => $this->message->created_at
        ]);
        
        Log::info('Broadcasting on channels', [
            'recipient_chat_channel' => 'chat.' . $this->message->recipient_id,
            'sender_chat_channel' => 'chat.' . $this->message->user_id,
            'recipient_user_channel' => 'user.' . $this->message->recipient_id,
            'sender_user_channel' => 'user.' . $this->message->user_id
        ]);
        
        // Broadcast to both users' chat and user channels
        return [
            // Recipient's chat channel
            new PrivateChannel('chat.' . $this->message->recipient_id),
            
            // Sender's chat channel
            new PrivateChannel('chat.' . $this->message->user_id),
            
            // Recipient's user channel
            new PrivateChannel('user.' . $this->message->recipient_id),
            
            // Sender's user channel
            new PrivateChannel('user.' . $this->message->user_id),
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        Log::info('Broadcasting message data', [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'user_id' => $this->message->user_id,
            'recipient_id' => $this->message->recipient_id
        ]);

        return [
            'message' => [
                'id' => $this->message->id,
                'content' => $this->message->content,
                'time' => $this->message->created_at->format('g:i A'),
                'date' => $this->message->created_at->format('Y-m-d'),
                'user_id' => $this->message->user_id,
                'recipient_id' => $this->message->recipient_id
            ]
        ];
    }
}
