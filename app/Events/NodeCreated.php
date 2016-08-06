<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NodeCreated extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $node;

    public $map;

    public $container;

    public $ip;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($node, $map, $container, $ip)
    {
        $this->node = $node;
        $this->map = $map;
        $this->container = $container;
        $this->ip = $ip;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['map'];
    }
}
