<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewOrderCreated implements ShouldBroadcast
{
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.User.' . $this->order->company_id);
    }

    public function broadcastAs()
    {
        return 'NewOrderCreated';
    }
}
