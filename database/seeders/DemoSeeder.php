<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // database/seeders/DemoSeeder.php
public function run(): void
{
    \App\Models\User::factory()->create([
        'name' => 'System Admin', 'email'=>'admin@pharma.test',
        'password'=>bcrypt('password'), 'role'=>'admin'
    ]);
    \App\Models\User::factory()->create([
        'name' => 'Libya Pharma Co', 'email'=>'company@pharma.test',
        'password'=>bcrypt('password'), 'role'=>'company'
    ]);
    \App\Models\User::factory()->create([
        'name' => 'صيدلية الأمل', 'email'=>'pharmacy@pharma.test',
        'password'=>bcrypt('password'), 'role'=>'pharmacy'
    ]);
    \App\Models\User::factory()->create([
        'name' => 'Ahmed Rep', 'email'=>'rep@pharma.test',
        'password'=>bcrypt('password'), 'role'=>'delivery'
    ]);
}

}
