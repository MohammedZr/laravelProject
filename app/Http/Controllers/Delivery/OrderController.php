<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Notifications\GlobalNotification;

class OrderController extends Controller
{
    public function updateStatus(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        // تحقق أن المندوب هو المسؤول عن الطلب
        abort_unless($order->delivery_user_id === auth()->id(), 403);

        $validated = $request->validate([
            'status' => 'required|string|in:out_for_delivery,completed,cancelled'
        ]);

        $order->update(['status' => $validated['status']]);

        // 🔔 إشعار الصيدلية بأن المندوب غيّر الحالة
        $pharmacy = User::where('id', $order->user_id)
                        ->where('role', 'pharmacy')
                        ->first();

        if ($pharmacy) {
            $pharmacy->notify(new GlobalNotification(
                'تحديث حالة الطلب',
                'قام المندوب بتغيير حالة طلبك إلى: ' . $order->status,
                route('pharmacy.orders.show', $order->id)
            ));
        }

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح ✅');
    }
}
