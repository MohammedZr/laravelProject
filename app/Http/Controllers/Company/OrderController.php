<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\GlobalNotification;
use App\Models\Pharmacy;
// use App\Models\Delivery;


class OrderController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->id();
        $status = $request->get('status');
        $search = $request->get('q');

        $orders = Order::query()
            ->with(['pharmacy:id,name', 'items.drug:id,name,generic_name,image_url'])
            ->where('company_id', $companyId)
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('id', $search)
                        ->orWhereHas('pharmacy', fn($p) => $p->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // عدد الحالات لتلخيص الواجهة
        $counts = Order::selectRaw('status, COUNT(*) as total')
            ->where('company_id', $companyId)
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return view('company.orders.index', compact('orders', 'counts', 'search', 'status'));
    }

    public function show(Order $order)
    {
        abort_unless($order->company_id === auth()->id(), 403);

        // جلب تفاصيل الطلب والعناصر
        $order->load(['pharmacy:id,name', 'items.drug:id,name,generic_name,image_url']);

        // جلب قائمة مندوبي الشركة فقط
        $couriers = User::where('role', 'delivery')
            ->where('company_id', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('company.orders.show', compact('order', 'couriers'));
    }

    public function updateStatus(Request $request, Order $order)
{
    abort_unless($order->company_id === auth()->id(), 403);

    $validated = $request->validate([
        'status' => 'required|string|in:pending,confirmed,preparing,out_for_delivery,completed,cancelled'
    ]);

    $order->update(['status' => $validated['status']]);

    // 🔔 إشعار الصيدلية بتحديث حالة الطلب
    $pharmacy = User::where('id', $order->user_id)
                    ->where('role', 'pharmacy')
                    ->first();

    if ($pharmacy) {
        $pharmacy->notify(new GlobalNotification(
            'تحديث حالة الطلب',
            'تم تغيير حالة طلبك إلى: ' . $order->status,
            route('pharmacy.orders.show', $order->id)
        ));
    }

    return back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
}


}
