<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // GET /delivery/tasks
    public function index(Request $request)
    {
        $courierId = Auth::id();
        $status = $request->get('status'); // اختياري: فلترة حسب الحالة

        $orders = Order::with(['pharmacy:id,name,lat,lng', 'company:id,name', 'items.drug:id,name,image_url,generic_name'])
            ->whereHas('delivery', function ($q) use ($courierId, $status) {
                $q->where('delivery_user_id', $courierId);
                if ($status) $q->where('status', $status);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('delivery.dashboard', [
            'title'  => 'مهامي',
            'orders' => $orders,
            'status' => $status,
        ]);
    }

    // GET /delivery/orders/{order}
    public function show(Order $order)
    {
        // تأكد أن الطلب مسند لهذا المندوب
        $delivery = $order->delivery;
        abort_unless($delivery && $delivery->delivery_user_id === Auth::id(), 403);

        $order->load(['pharmacy:id,name,lat,lng', 'company:id,name', 'items.drug:id,name,generic_name,image_url']);

        // إحداثي الهدف: أولوية لإحداثيات الطلب، ثم للصيدلية
        $targetLat = $order->delivery_lat ?? $order->pharmacy->lat ?? null;
        $targetLng = $order->delivery_lng ?? $order->pharmacy->lng ?? null;

        return view('delivery.orders.show', [
            'title'     => "طلب #{$order->id}",
            'order'     => $order,
            'targetLat' => $targetLat,
            'targetLng' => $targetLng,
        ]);
    }

    // PATCH /delivery/orders/{order}/status
    public function updateStatus(Request $request, Order $order)
    {
        $delivery = $order->delivery;
        abort_unless($delivery && $delivery->delivery_user_id === Auth::id(), 403);

        $validated = $request->validate([
            'status' => 'required|string|in:out_for_delivery,completed'
        ]);

        // لو جاي يكمل التسليم، إحفظ توقيت التسليم
        if ($validated['status'] === 'completed') {
            $delivery->status = 'delivered';
            $delivery->delivered_at = now();
            $delivery->save();

            // قفل الطلب مكتمل
            $order->status = 'completed';
            $order->save();

            return back()->with('success', '✅ تم تأكيد التسليم بنجاح.');
        }

        // لو بدأ التوصيل
        if ($validated['status'] === 'out_for_delivery') {
            $order->status = 'out_for_delivery';
            $order->save();

            $delivery->status = 'out_for_delivery';
            $delivery->save();

            return back()->with('success', '🚚 تم بدء عملية التسليم.');
        }

        return back()->with('status', 'لا يوجد تغيير.');
    }

    // GET /delivery/orders/{order}/print
    public function print(Order $order)
    {
        $delivery = $order->delivery;
        abort_unless($delivery && $delivery->delivery_user_id === Auth::id(), 403);

        $order->load(['pharmacy:id,name', 'company:id,name', 'items.drug:id,name']);

        return view('delivery.orders.print', [
            'order' => $order,
            'title' => "طباعة طلب #{$order->id}",
        ]);
    }
}
