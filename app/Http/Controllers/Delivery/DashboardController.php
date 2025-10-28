<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // GET /delivery/dashboard
    public function index(Request $request)
    {
        $courierId = auth()->id();
        $status = $request->query('status');

        $orders = Order::with(['pharmacy:id,name','delivery'])
            ->whereHas('delivery', fn($q)=>$q->where('delivery_user_id', $courierId)
                                             ->when($status, fn($qq)=>$qq->where('status',$status)))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $title = 'لوحة المندوب';
        return view('delivery.dashboard', compact('orders','status','title'));
    }

    // GET /delivery/orders/{order}
    public function show(Order $order)
    {
        $this->authorizeCourier($order);
        $order->load(['pharmacy:id,name,email','delivery','items.drug']);
        $title = "توصيل الطلب #{$order->id}";
        return view('delivery.show', compact('order','title'));
    }

    // PATCH /delivery/orders/{order}/status
    public function updateStatus(Request $request, Order $order)
    {
        $this->authorizeCourier($order);

        $validated = $request->validate([
            'status' => 'required|in:picked_up,delivering,delivered,failed',
            'failed_reason' => 'nullable|string|max:255',
        ]);

        $delivery = $order->delivery;
        abort_if(!$delivery, 400);

        $delivery->status = $validated['status'];
        if ($validated['status'] === 'picked_up')  $delivery->picked_up_at = now();
        if ($validated['status'] === 'delivered')  $delivery->delivered_at = now();
        if ($validated['status'] === 'failed')     $delivery->failed_reason = $validated['failed_reason'] ?? '—';
        $delivery->save();

        // تزامن مع حالة الطلب إن رغبت:
        if ($validated['status'] === 'delivering')  $order->update(['status' => 'out_for_delivery']);
        if ($validated['status'] === 'delivered')   $order->update(['status' => 'completed']);
        if ($validated['status'] === 'failed')      $order->update(['status' => 'cancelled']);

        return back()->with('success', 'تم تحديث حالة التوصيل.');
    }

    protected function authorizeCourier(Order $order): void
    {
        abort_unless(optional($order->delivery)->delivery_user_id === auth()->id(), 403);
    }
}
