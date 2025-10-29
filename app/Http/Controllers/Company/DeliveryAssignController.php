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
        // ✅ تأكيد ملكية الشركة للطلب
        abort_unless($order->company_id === auth()->id(), 403);

        $data = $request->validate([
            'courier_id' => ['required', 'exists:users,id'],
        ]);

        $courier = User::where('id', $data['courier_id'])
            ->where('role', 'delivery')
            ->where('company_id', auth()->id()) // فقط مندوبي الشركة الحالية
            ->firstOrFail();

        // ✅ تحديث أو إنشاء سجل التوصيل
        Delivery::updateOrCreate(
            ['order_id' => $order->id],
            [
                'company_id'       => auth()->id(),
                'delivery_user_id' => $courier->id,
                'status'           => 'assigned',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]
        );

        // ✅ يمكن أيضًا تحديث حالة الطلب مباشرة (اختياري)
        $order->update(['status' => 'out_for_delivery']);

        return back()->with('success', 'تم إسناد الطلب إلى المندوب بنجاح.');
    }

    public function unassign(Order $order)
    {
        abort_unless($order->company_id === auth()->id(), 403);

        optional($order->delivery)->delete();
        $order->update(['status' => 'pending']);

        return back()->with('success', 'تم إلغاء الإسناد.');
    }
}
