<?php

// app/Http/Controllers/Delivery/TaskController.php
namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::with(['order.pharmacy','company'])
            ->where('delivery_user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('delivery.tasks.index', [
            'title' => 'مهامي',
            'deliveries' => $deliveries,
        ]);
    }

    public function accept(Delivery $delivery)
    {
        abort_unless($delivery->delivery_user_id === auth()->id(), 403);
        if ($delivery->status === 'assigned') {
            $delivery->update(['status' => 'accepted']);
        }
        return back();
    }

    public function start(Delivery $delivery)
    {
        abort_unless($delivery->delivery_user_id === auth()->id(), 403);
        if (in_array($delivery->status, ['assigned','accepted'], true)) {
            $delivery->update(['status' => 'in_transit', 'picked_up_at' => now()]);
            // مزامنة حالة الطلب
            optional($delivery->order)->update(['status' => 'out_for_delivery']);
        }
        return back();
    }

    public function complete(Delivery $delivery)
    {
        abort_unless($delivery->delivery_user_id === auth()->id(), 403);
        if ($delivery->status === 'in_transit') {
            $delivery->update(['status' => 'delivered', 'delivered_at' => now()]);
            optional($delivery->order)->update(['status' => 'completed']);
        }
        return back();
    }
}
