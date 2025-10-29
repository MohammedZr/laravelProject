<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class NewOrderCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    // نستخدم قناة عامة باسم company.orders
    public function broadcastOn()
    {
        return new Channel('company.orders');
    }

    // اسم الحدث في JavaScript
    public function broadcastAs()
    {
        return 'order.created';
    }
}
