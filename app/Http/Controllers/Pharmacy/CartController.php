<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Drug;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show()
    {
        $cart = Cart::forUser(auth()->id())->load(['items.drug','items.company']);
        // تجميع العناصر حسب الشركة
        $byCompany = $cart->items->groupBy('company_id');
        return view('pharmacy.cart', compact('cart','byCompany'));
    }

    public function add(Request $request, Drug $drug)
    {
        abort_unless($drug->is_active, 403);

        $data = $request->validate([
            'quantity' => ['nullable','integer','min:1'],
        ]);
        $qty = $data['quantity'] ?? 1;

        $cart = Cart::forUser(auth()->id());

        $item = CartItem::firstOrNew([
            'cart_id'   => $cart->id,
            'drug_id'   => $drug->id,
        ], [
            'company_id'=> $drug->user_id,
            'unit_price'=> $drug->price,
        ]);

        if ($item->exists) {
            $item->quantity += $qty;
        } else {
            $item->quantity = $qty;
        }

        $item->company_id = $drug->user_id; // تأكيد
        $item->unit_price = $drug->price;   // التقط السعر الحالي
        $item->save();

        return back()->with('status', 'تمت إضافة الدواء إلى السلة');
    }

    public function update(Request $request, CartItem $item)
    {
        abort_unless($item->cart->user_id === auth()->id(), 403);

        $data = $request->validate([
            'quantity' => ['required','integer','min:1'],
        ]);
        $item->update(['quantity' => $data['quantity']]);

        return back()->with('status', 'تم تحديث الكمية');
    }

    public function remove(CartItem $item)
    {
        abort_unless($item->cart->user_id === auth()->id(), 403);
        $item->delete();
        return back()->with('status', 'تم حذف العنصر من السلة');
    }
}
