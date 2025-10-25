<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DrugGroup;
use App\Models\Drug;
use Illuminate\Database\Seeder;

class CommercialDemoSeeder extends Seeder
{
    public function run(): void
    {
        // شركة تجريبية (لو موجودة ما يعيدهاش)
        $company = User::firstOrCreate(
            ['email' => 'company@pharma.test'],
            ['name' => 'Libya Pharma Co', 'password'=>bcrypt('password'), 'role'=>'company']
        );

        // مجموعة/درفت
        $group = DrugGroup::firstOrCreate(
            ['user_id'=>$company->id, 'title'=>'دفعة أكتوبر'],
            ['status'=>'draft', 'notes'=>'إضافة منتجات مسكنات وحساسية']
        );

        // أدوية ثابتة نموذجية
        $drugs = [
            ['name'=>'Panadol', 'generic'=>'Paracetamol', 'form'=>'Tablet', 'str'=>'500mg', 'unit'=>'tabs'],
            ['name'=>'Brufen', 'generic'=>'Ibuprofen', 'form'=>'Tablet', 'str'=>'400mg', 'unit'=>'tabs'],
            ['name'=>'Voltaren', 'generic'=>'Diclofenac', 'form'=>'Gel', 'str'=>'1%', 'unit'=>'g'],
            ['name'=>'Zyrtec', 'generic'=>'Cetirizine', 'form'=>'Syrup', 'str'=>'1mg/ml', 'unit'=>'ml'],
            ['name'=>'Augmentin', 'generic'=>'Amoxicillin/Clavulanate', 'form'=>'Tablet', 'str'=>'1g', 'unit'=>'tabs'],
        ];

        foreach ($drugs as $d) {
            Drug::firstOrCreate(
                [
                    'user_id'       => $company->id,
                    'drug_group_id' => $group->id,
                    'name'          => $d['name'],
                    'strength'      => $d['str'],
                ],
                [
                    'generic_name'  => $d['generic'],
                    'dosage_form'   => $d['form'],
                    'pack_size'     => 20,
                    'unit'          => $d['unit'],
                    'sku'           => strtoupper('SKU-'.substr(md5($d['name'].$d['str']),0,6)),
                    'barcode'       => null,
                    'price'         => mt_rand(10,80),
                    'stock'         => mt_rand(10,300),
                    'is_active'     => false,
                ]
            );
        }

        // تقدر كمان تولّد عشوائيًا من الفاكتوري:
        // Drug::factory()->count(20)->create([
        //     'user_id' => $company->id,
        //     'drug_group_id' => $group->id,
        // ]);
    }
}
