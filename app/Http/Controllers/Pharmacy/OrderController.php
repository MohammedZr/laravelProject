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
use App\Models\User;   // شركة

class OrderController extends Controller
{
    // GET /pharmacy/orders
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

    // POST /pharmacy/orders/checkout/{company}
    public function checkoutCompany(Request $request, $companyId)
    {
        $pharmacy = Auth::user();

        // كرت مفتوح للصيدلية
        $cart = Cart::firstOrCreate([
            'user_id' => $pharmacy->id,
            'status'  => 'open',
        ]);

        // عناصر السلة الخاصة بهذه الشركة فقط
        $items = CartItem::with(['drug:id,user_id,price'])
            ->where('cart_id', $cart->id)
            ->whereHas('drug', fn($q) => $q->where('user_id', $companyId))
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'لا توجد عناصر تخص هذه الشركة في السلة.');
        }

        // حساب الإجمالي وإنشاء الطلب + عناصره في ترانزاكشن
        DB::transaction(function () use ($items, $pharmacy, $companyId, $cart) {
            $total = $items->reduce(function ($carry, $it) {
                $unit = $it->unit_price ?? optional($it->drug)->price ?? 0;
                return $carry + ($unit * $it->quantity);
            }, 0);

            $order = Order::create([
                'user_id'     => $pharmacy->id,
                'company_id'  => $companyId,
                'status'      => 'pending',
                'total_amount'=> $total,
            ]);

            foreach ($items as $it) {
                $unit = $it->unit_price ?? optional($it->drug)->price ?? 0;
                OrderItem::create([
                    'order_id'   => $order->id,
                    'drug_id'    => $it->drug_id,
                    'quantity'   => $it->quantity,
                    'unit_price' => $unit,
                    'line_total' => $unit * $it->quantity,
                ]);
            }

            // حذف العناصر التي تم طلبها من السلة (الخاصة بهذه الشركة فقط)
            CartItem::where('cart_id', $cart->id)
                ->whereHas('drug', fn($q) => $q->where('user_id', $companyId))
                ->delete();
        });

        return redirect()->route('pharmacy.orders.index')
            ->with('ok', 'تم إرسال الطلبية للشركة بنجاح.');
    }
}
