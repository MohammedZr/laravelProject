<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class DeliveryAssigned implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public Order $order;
    public int $deliveryUserId;

    public function __construct(Order $order, int $deliveryUserId)
    {
        $this->order = $order->load(['pharmacy:id,name', 'company:id,name']);
        $this->deliveryUserId = $deliveryUserId;
    }

    public function broadcastOn(): array
    {
        // قناة المندوب المستهدف
        return [new PrivateChannel('App.Models.User.' . $this->deliveryUserId)];
    }

    public function broadcastAs(): string
    {
        return 'DeliveryAssigned';
    }

    public function broadcastWith(): array
    {
        return [
            'order' => [
                'id'      => $this->order->id,
                'status'  => $this->order->status,
                'total'   => (float) $this->order->total_amount,
                'company' => [
                    'id'   => $this->order->company->id ?? null,
                    'name' => $this->order->company->name ?? null,
                ],
                'pharmacy' => [
                    'id'   => $this->order->pharmacy->id ?? null,
                    'name' => $this->order->pharmacy->name ?? null,
                ],
            ]
        ];
    }
}
