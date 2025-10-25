<?php

namespace Database\Factories;

use App\Models\Drug;
use Illuminate\Database\Eloquent\Factories\Factory;

class DrugFactory extends Factory
{
    protected $model = Drug::class;

    public function definition(): array
    {
        $brand = fake()->randomElement(['Panadol','Augmentin','Amoxil','Voltaren','Brufen','Zyrtec','Claritin']);
        $generic = fake()->randomElement(['Paracetamol','Amoxicillin','Diclofenac','Ibuprofen','Cetirizine','Loratadine']);
        $forms = ['Tablet','Capsule','Syrup','Injection','Cream','Gel'];
        $strengths = ['100mg','200mg','250mg','400mg','500mg','1g/5ml'];

        return [
            'name'         => $brand,
            'generic_name' => $generic,
            'dosage_form'  => fake()->randomElement($forms),
            'strength'     => fake()->randomElement($strengths),
            'pack_size'    => fake()->numberBetween(10, 30),
            'unit'         => 'tabs',
            'sku'          => strtoupper(fake()->bothify('SKU-####??')),
            'barcode'      => fake()->unique()->ean13(),
            'price'        => fake()->randomFloat(2, 3, 80),
            'stock'        => fake()->numberBetween(0, 500),
            'is_active'    => false,
        ];
    }
}
