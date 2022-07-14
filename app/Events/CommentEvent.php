<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use App\Models\Post;

class CommentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $comment;
    public $post_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($comment, $id)
    {
        $this->comment = $comment;
        $this->post_id = $id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $post_id = $this->post_id;
        return new PrivateChannel('Post' . $post_id);
    }
    public function broadcastWith()
    {
        return [
            "id" => $this->comment['id'],
            "content" => $this->comment['content'],
            "user_id" => $this->comment['user_id'],
            "created_at" => $this->comment['created_at']

        ];
    }
}
