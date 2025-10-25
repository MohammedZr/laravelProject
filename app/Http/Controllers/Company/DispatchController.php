<?php

// app/Http/Controllers/Company/DispatchController.php
namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class DispatchController extends Controller
{
    public function assign(Request $request, Order $order)
    {
        // تحقق ملكية الطلب للشركة الحالية
        abort_unless(auth()->id() === (int) $order->company_id, 403);

        // تحقق من المنذوب
        $data = $request->validate([
            'delivery_user_id' => ['required','integer','exists:users,id'],
            'notes' => ['nullable','string','max:1000'],
        ]);

        $courier = User::where('id', $data['delivery_user_id'])
            ->where('role','delivery')
            ->where('company_id', auth()->id()) // لازم يتبع لنفس الشركة
            ->firstOrFail();

        // أنشئ أو حدّث مهمة التسليم
        $delivery = Delivery::updateOrCreate(
            ['order_id' => $order->id],
            [
                'company_id'       => auth()->id(),
                'delivery_user_id' => $courier->id,
                'status'           => 'assigned',
                'notes'            => $data['notes'] ?? null,
            ]
        );

        // اختياري: عدّل حالة الطلب لو حاب
        if ($order->status === 'confirmed' || $order->status === 'preparing') {
            // نتركها كما هي. نغيّر للحركة لاحقًا عندما يبدأ المنذوب.
        }

        return back()->with('ok','تم إسناد الطلب للمنذوب بنجاح.');
    }
}
