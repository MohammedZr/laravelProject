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
use App\Events\NewOrderCreated;

class OrderController extends Controller
{
    /**
     * عرض جميع طلبات الصيدلية
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
     * إرسال الطلب إلى شركة محددة من السلة
     */
    public function checkoutCompany(Request $request, User $company)
    {
        abort_unless($company->role === 'company', 403);

        $pharmacy = $request->user();

        // 🔹 جلب السلة المفتوحة
        $cart = Cart::where('user_id', $pharmacy->id)
            ->where('status', 'open')
            ->first();

        if (!$cart) {
            return back()->with('error', '⚠️ لا توجد سلة حالياً.');
        }

        // 🔹 عناصر السلة التابعة للشركة المحددة
        $cartItems = CartItem::where('cart_id', $cart->id)
            ->whereHas('drug', fn($q) => $q->where('company_id', $company->id))
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', '⚠️ السلة فارغة لهذه الشركة.');
        }

        // 🔹 إنشاء الطلب داخل معاملة واحدة
        $order = DB::transaction(function () use ($pharmacy, $company, $cartItems) {
            $total = $cartItems->sum(fn($item) => $item->unit_price * $item->quantity);

            $order = Order::create([
                'user_id'      => $pharmacy->id,
                'company_id'   => $company->id,
                'status'       => 'pending',
                'total_amount' => $total,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'drug_id'    => $item->drug_id,
                    'quantity'   => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->unit_price * $item->quantity,
                ]);
            }

            return $order;
        });

        // 🔹 حذف العناصر من السلة بعد إنشاء الطلب
        foreach ($cartItems as $item) {
            $item->delete();
        }

        // 🔹 بث الإشعار للشركة
        event(new NewOrderCreated($order));

        // 🔹 توجيه المستخدم إلى صفحة النجاح
        return redirect()
            ->route('pharmacy.orders.success', $order)
            ->with('success', '✅ تم إرسال الطلبية بنجاح، وبُعث إشعار للشركة.');
    }

    /**
     * عرض تفاصيل الطلب
     */
    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load(['company:id,name', 'items.drug:id,name,generic_name,image_url']);

        $targetLat = $order->delivery_lat ?? optional($order->pharmacy)->lat;
        $targetLng = $order->delivery_lng ?? optional($order->pharmacy)->lng;

        return view('pharmacy.orders.show', [
            'order' => $order,
            'title' => "تفاصيل الطلب #{$order->id}",
            'targetLat' => $targetLat,
            'targetLng' => $targetLng,
        ]);
    }

    /**
     * صفحة النجاح بعد إرسال الطلب
     */
    public function success(Order $order)
    {
        return view('pharmacy.orders.success', [
            'title' => 'تم إرسال الطلب',
            'order' => $order,
        ]);
    }
}
