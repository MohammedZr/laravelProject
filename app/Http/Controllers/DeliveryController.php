<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Delivery;

class DeliveryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function myTasks(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'delivery') {
            abort(403, 'Forbidden');
        }

        $orders = Order::with(['user','company','items','delivery'])
            ->whereHas('delivery', function ($q) use ($user) {
                $q->where('delivery_user_id', $user->id);
            })
            ->orderByDesc('id')->get();

        return view('delivery.tasks', compact('orders'));
    }

    public function assign(Request $request, Order $order)
    {
        $user = $request->user();

        if (!in_array($user->role, ['company','admin'])) {
            abort(403, 'Forbidden');
        }

        $request->validate([
            'delivery_user_id' => 'required|exists:users,id',
        ]);

        Delivery::updateOrCreate(
            ['order_id' => $order->id],
            [
                'company_id'       => $user->id,
                'delivery_user_id' => $request->delivery_user_id,
                'status'           => 'assigned',
            ]
        );

        if ($order->status === 'pending') {
            $order->update(['status' => 'out_for_delivery']);
        }

        return back()->with('success','تم إسناد الطلب بنجاح للمندوب.');
    }
}
