<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // GET /company/orders
    public function index(Request $request)
    {
        $companyId = auth()->id();

        $status = $request->query('status'); // pending, confirmed, preparing, out_for_delivery, completed, cancelled
        $search = $request->query('q');      // رقم الطلب أو اسم الصيدلية

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

        $counts = Order::selectRaw('status, COUNT(*) as c')
            ->where('company_id', $companyId)
            ->groupBy('status')->pluck('c','status');

        $title = 'إدارة الطلبيات';
        return view('company.orders.index', compact('orders','counts','status','search','title'));
    }

    // GET /company/orders/{order}
    public function show(Order $order)
    {
        $this->authorizeCompany($order);
        $order->load(['pharmacy:id,name,email','items.drug']);
        $title = "تفاصيل الطلب #{$order->id}";
        return view('company.orders.show', compact('order','title'));
    }

    // PATCH /company/orders/{order}/status
    public function updateStatus(Request $request, Order $order)
    {
        $this->authorizeCompany($order);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,out_for_delivery,completed,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        return back()->with('success', 'تم تحديث حالة الطلب.');
    }

    protected function authorizeCompany(Order $order): void
    {
        abort_unless($order->company_id === auth()->id(), 403);
    }
}
