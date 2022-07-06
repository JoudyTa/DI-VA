<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Post;

class PostEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /*  public $photo;
    public $user_id;
    public $interest_id;
    public $content;
    public $is_prometed; */
    public $post;

    /**
     * Create a new event instance.
     *
     * @return void
     */


    public function __construct(Post $post)
    {
        $this->post = $post;
        //$post = Post::find($id);
        /*    $this->photo = $post->photo;
        $this->user_id = $post->user_id;
        $this->interest_id = $post->interest_id;
        $this->content = $post->content;
        $this->is_prometed = $post->is_prometed; */
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */

    public function broadcastOn()
    {

        return new PrivateChannel('Post'); //, $this->post->user_id);
    }
    public function broadcastWith()
    {
        return [
            'photo' => $this->post->photo,
            'user_id' => $this->post->user_id,
            'created_at' => $this->post->created_at->toFormattedDateString(),
            'interest_id' => $this->post->interest_id,
            'content' => $this->post->content,
            'is_prometed' => $this->post->is_prometed
        ];
    }
}
