<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public Order $order;
    public string $newStatus;

    public function __construct(Order $order, string $newStatus)
    {
        $this->order = $order->load(['pharmacy:id,name', 'company:id,name']);
        $this->newStatus = $newStatus;
    }

    public function broadcastOn(): array
    {
        // نختار المستلمين حسب نوع الحالة
        $channels = [];

        // دائمًا الشركة تعرف بالحالة
        $channels[] = new PrivateChannel('App.Models.User.' . $this->order->company_id);

        // الصيدلية تهتم عادةً بحالة التوصيل
        $channels[] = new PrivateChannel('App.Models.User.' . $this->order->user_id);

        // إن أردت إضافة المندوب عند حالات معينة (مثلا completed) اجلب delivery_user_id لو متوفر:
        if (optional($this->order->delivery)->delivery_user_id) {
            $channels[] = new PrivateChannel('App.Models.User.' . $this->order->delivery->delivery_user_id);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'OrderStatusUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'order' => [
                'id'      => $this->order->id,
                'status'  => $this->newStatus,
                'total'   => (float) $this->order->total_amount,
                'pharmacy'=> ['id' => $this->order->pharmacy->id ?? null, 'name' => $this->order->pharmacy->name ?? null],
                'company' => ['id' => $this->order->company->id ?? null, 'name' => $this->order->company->name ?? null],
            ]
        ];
    }
}
