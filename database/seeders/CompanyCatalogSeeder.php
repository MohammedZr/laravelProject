<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DrugGroup;
use App\Models\Drug;
use Illuminate\Database\Seeder;

class CompanyCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $company = User::where('email', 'company@pharma.test')->firstOrFail();

        // مجموعة (draft)
        $group = DrugGroup::updateOrCreate(
            ['user_id' => $company->id, 'title' => 'دفعة أكتوبر'],
            ['status' => 'published'] // خلّيناها منشورة علشان الأدوية المفعّلة تظهر فورًا
        );

        // لائحة أدوية نموذجية
        $drugs = [
            [
                'name' => 'Panadol',
                'generic_name' => 'Paracetamol',
                'dosage_form' => 'Tablet',
                'strength' => '500mg',
                'pack_size' => 24,
                'unit' => 'tabs',
                'sku' => 'PAN-500-TAB',
                'barcode' => '629123450001',
                'price' => 4.50,
                'stock' => 500,
                'is_active' => true,
            ],
            [
                'name' => 'Augmentin',
                'generic_name' => 'Amoxicillin + Clavulanic Acid',
                'dosage_form' => 'Tablet',
                'strength' => '1g',
                'pack_size' => 14,
                'unit' => 'tabs',
                'sku' => 'AUG-1G-TAB',
                'barcode' => '629123450002',
                'price' => 18.90,
                'stock' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Ventolin',
                'generic_name' => 'Salbutamol',
                'dosage_form' => 'Inhaler',
                'strength' => '100mcg/dose',
                'pack_size' => 1,
                'unit' => 'pcs',
                'sku' => 'VEN-100-MDI',
                'barcode' => '629123450003',
                'price' => 9.75,
                'stock' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Omeprazole',
                'generic_name' => 'Omeprazole',
                'dosage_form' => 'Capsule',
                'strength' => '20mg',
                'pack_size' => 28,
                'unit' => 'caps',
                'sku' => 'OME-20-CAP',
                'barcode' => '629123450004',
                'price' => 7.90,
                'stock' => 200,
                'is_active' => true,
            ],
            [
                'name' => 'Ibuprofen',
                'generic_name' => 'Ibuprofen',
                'dosage_form' => 'Tablet',
                'strength' => '400mg',
                'pack_size' => 20,
                'unit' => 'tabs',
                'sku' => 'IBU-400-TAB',
                'barcode' => '629123450005',
                'price' => 5.25,
                'stock' => 350,
                'is_active' => true,
            ],
            // دواء غير مفعّل لن يظهر في البحث
            [
                'name' => 'Cough Syrup X',
                'generic_name' => 'Dextromethorphan',
                'dosage_form' => 'Syrup',
                'strength' => '15mg/5ml',
                'pack_size' => 1,
                'unit' => 'bottle',
                'sku' => 'COU-15-SYR',
                'barcode' => '629123450006',
                'price' => 6.40,
                'stock' => 40,
                'is_active' => false,
            ],
        ];

        foreach ($drugs as $d) {
            Drug::updateOrCreate(
                [
                    'user_id' => $company->id,
                    'drug_group_id' => $group->id,
                    'name' => $d['name'],
                    'strength' => $d['strength'] ?? null,
                ],
                $d
            );
        }
    }
}
