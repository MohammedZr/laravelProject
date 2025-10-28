<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\DrugGroup;
use App\Models\Drug;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Delivery;

class OrderWithLocationSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1) مستخدمو الأساس (شركة/صيدلية/مندوب)
            $company = User::firstOrCreate(
                ['email' => 'company@pharma.test'],
                ['name' => 'Libya Pharma Co', 'password'=>Hash::make('password'), 'role'=>'company']
            );

            $pharmacy = User::firstOrCreate(
                ['email' => 'pharmacy@pharma.test'],
                ['name' => 'صيدلية الأمل', 'password'=>Hash::make('password'), 'role'=>'pharmacy']
            );

            $deliveryUser = User::firstOrCreate(
                ['email' => 'driver@pharma.test'],
                ['name' => 'مندوب طرابلس', 'password'=>Hash::make('password'), 'role'=>'delivery']
            );

            // 2) مجموعة (Catalog/Draft) للشركة حتى لا يكون drug_group_id = null
            $group = DrugGroup::firstOrCreate(
                ['user_id' => $company->id, 'title' => 'Default Catalog'],
                ['status' => 'published'] // أو draft حسب نظامك
            );

            // 3) دواء تابع للشركة ضمن المجموعة
            $drug = Drug::firstOrCreate(
                [
                    'user_id'       => $company->id,
                    'drug_group_id' => $group->id,
                    'name'          => 'Augmentin 625',
                ],
                [
                    'generic_name'  => 'Amoxicillin/Clavulanate',
                    'dosage_form'   => 'Tablet',
                    'strength'      => '625mg',
                    'pack_size'     => 14,
                    'unit'          => 'tabs',
                    'sku'           => 'AUG-625-TAB',
                    'barcode'       => '1234567890123',
                    'price'         => 15.00,
                    'stock'         => 500,
                    'image_url'     => null, // لو عمودك يسمح NULL. إن ما يسمح، ضع ''
                ]
            );

            // 4) إنشاء الطلب بعنوان + إحداثيات
            $order = Order::create([
                'user_id'   => $pharmacy->id,   // الصيدلية
                'company_id'=> $company->id,    // الشركة
                'status'    => 'confirmed',
                'total_amount' => 0, // نحسب بعد العناصر
                'delivery_address_line' => 'شارع الاستقلال، بالقرب من ميدان الشهداء',
                'delivery_city'         => 'طرابلس',
                'delivery_phone'        => '+218912345678',
                'delivery_lat'          => 32.8954312,
                'delivery_lng'          => 13.1801612,
            ]);

            // 5) عنصر طلب
            $qty  = 3;
            $unit = $drug->price ?? 0;
            OrderItem::create([
                'order_id'   => $order->id,
                'drug_id'    => $drug->id,
                'quantity'   => $qty,
                'unit_price' => $unit,
                'line_total' => $unit * $qty,
            ]);

            // تحديث الإجمالي
            $total = OrderItem::where('order_id', $order->id)->sum('line_total');
            $order->update(['total_amount' => $total]);

            // 6) إنشاء مهمة تسليم وربطها بالمندوب
            Delivery::create([
                'order_id'         => $order->id,
                'company_user_id'  => $company->id,
                'delivery_user_id' => $deliveryUser->id,
                'status'           => 'assigned', // assigned | accepted | picked_up | delivered | cancelled
                'assigned_at'      => now(),
            ]);
        });
    }
}
