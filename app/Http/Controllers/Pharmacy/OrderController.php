<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Events\NewOrderCreated; // ✅ أضف هذا السطر للبث

class OrderController extends Controller
{
    /**
     * عرض طلبات الصيدلية
     * GET /pharmacy/orders
     */
    public function index()
    {
        $user = Auth::user();

        $orders = Order::with(['company:id,name', 'items.drug:id,name,generic_name,image_url'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(12);

        return view('pharmacy.orders.index', [
            'title'  => 'طلبات الصيدلية',
            'orders' => $orders,
        ]);
    }

    /**
     * إرسال الطلب إلى شركة محددة من سلة الصيدلية
     * POST /pharmacy/orders/checkout/{company}
     */
public function checkoutCompany(Request $request, User $company)
{
    abort_unless($company->role === 'company', 403);

    $pharmacy = $request->user();

    // ✅ جلب السلة المفتوحة للمستخدم الحالي
    $cart = \App\Models\Cart::where('user_id', $pharmacy->id)
                ->where('status', 'open')
                ->first();

    if (!$cart) {
        return back()->with('status', '⚠️ لا توجد سلة حالياً.');
    }

    // ✅ جلب العناصر الخاصة بهذه الشركة فقط
    $cartItems = \App\Models\CartItem::where('cart_id', $cart->id)
                    ->whereHas('drug', fn($q) => $q->where('company_id', $company->id))
                    ->get();

    if ($cartItems->isEmpty()) {
        return back()->with('status', '⚠️ السلة فارغة لهذه الشركة.');
    }

    // ✅ إنشاء الطلب داخل معاملة واحدة
    $order = DB::transaction(function () use ($pharmacy, $company, $cartItems) {
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->unit_price * $item->quantity;
        }

        $order = \App\Models\Order::create([
            'user_id'      => $pharmacy->id,
            'company_id'   => $company->id,
            'status'       => 'pending',
            'total_amount' => $total,
        ]);

        foreach ($cartItems as $item) {
            \App\Models\OrderItem::create([
                'order_id'   => $order->id,
                'drug_id'    => $item->drug_id,
                'quantity'   => $item->quantity,
                'unit_price' => $item->unit_price,
                'line_total' => $item->unit_price * $item->quantity,  // ✅ أضفنا هذا السطر
                    
            ]);
        }

        $order->load(['pharmacy:id,name,email', 'items.drug:id,name,generic_name,image_url']);
        return $order;
    });

    // ✅ حذف العناصر التي تم إرسالها من السلة
    foreach ($cartItems as $item) {
        $item->delete();
    }

    // ✅ بثّ الإشعار للشركة
    event(new \App\Events\NewOrderCreated($order));

    return redirect()
        ->route('pharmacy.orders.show', $order)
        ->with('status', '✅ تم إرسال الطلبية بنجاح، وبُعث إشعار للشركة.');
}
public function show(Order $order)
{
    abort_unless($order->user_id === auth()->id(), 403);

    // تحميل العلاقات المرتبطة
    $order->load(['company:id,name', 'items.drug:id,name,generic_name,image_url']);

    // تحديد الموقع الهدف (إحداثيات التسليم)
    $targetLat = $order->delivery_lat ?? $order->pharmacy->lat ?? null;
    $targetLng = $order->delivery_lng ?? $order->pharmacy->lng ?? null;

    return view('pharmacy.orders.show', [
        'order' => $order,
        'title' => "تفاصيل الطلب #{$order->id}",
        'targetLat' => $targetLat,
        'targetLng' => $targetLng,
    ]);
}


}
