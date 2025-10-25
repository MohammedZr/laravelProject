<?php

// database/seeders/DeliveryDemoSeeder.php
// database/seeders/DeliveryDemoSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DeliveryDemoSeeder extends Seeder
{
    public function run(): void
    {
        $company = User::where('role','company')->first();
        if (!$company) return;

        User::firstOrCreate(
            ['email' => 'rep1@pharma.test'],
            [
                'name' => 'Courier One',
                'password' => bcrypt('password'),
                'role' => 'delivery',
                'company_id' => $company->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'rep2@pharma.test'],
            [
                'name' => 'Courier Two',
                'password' => bcrypt('password'),
                'role' => 'delivery',
                'company_id' => $company->id,
            ]
        );
    }
}
