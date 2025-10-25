<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Delivery;
use Illuminate\Http\Request;

class DeliveryAssignController extends Controller
{
    public function assign(Request $request, Order $order)
    {
        // تأكد أن الشركة المالكة للطلب هي الحالية
        abort_unless($order->company_id === auth()->id(), 403);

        $data = $request->validate([
            'courier_id' => ['required','exists:users,id']
        ]);

        $courier = User::where('id', $data['courier_id'])
            ->where('role', 'delivery')
            ->where('company_id', auth()->id()) // مندوب تابع لنفس الشركة
            ->firstOrFail();

        Delivery::updateOrCreate(
            ['order_id' => $order->id],
            ['courier_id' => $courier->id, 'status' => 'assigned', 'assigned_at' => now()]
        );

        // ممكن تحدّث حالة الطلب داخليًا لو حاب (تبقى pending حتى يستلم)
        return back()->with('success','تم إسناد الطلب إلى المندوب.');
    }

    public function unassign(Order $order)
    {
        abort_unless($order->company_id === auth()->id(), 403);
        optional($order->delivery)->delete();
        return back()->with('success','تم إلغاء الإسناد.');
    }
}
