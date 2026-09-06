<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $cost = fake()->randomFloat(2, 1, 50);

        return [
            'sku' => strtoupper(fake()->unique()->bothify('SKU-#####')),
            'name' => fake()->unique()->words(3, true),
            'category_id' => null,
            'unit_id' => null,
            'cost_price' => $cost,
            'selling_price' => round($cost * 1.8, 2),
            'reorder_level' => fake()->numberBetween(5, 20),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
