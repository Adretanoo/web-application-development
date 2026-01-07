<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $user;

    public function __construct(Message $message)
    {
        $this->message = $message->message;
        $this->user = $message->user->name;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('chat');
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }
}
