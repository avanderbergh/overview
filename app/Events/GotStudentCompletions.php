<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GotStudentCompletions implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $realm_id;
    public $enrollment;

    /**
     * Create a new event instance.
     *
     * @param $user_id
     * @param $realm_id
     * @param $enrollment
     */
    public function __construct($user_id, $realm_id, $enrollment)
    {
        $this->user_id = $user_id;
        $this->realm_id = $realm_id;
        $this->enrollment = $enrollment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('students.'.$this->realm_id.'.'.$this->user_id);
    }
}
