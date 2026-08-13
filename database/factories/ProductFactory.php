<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $sku = 'SKU-'.fake()->unique()->numerify('######');

        return [
            'category_id' => Category::query()->create([
                'pos_category_id' => 'CAT-'.fake()->unique()->numerify('####'),
                'name' => fake()->words(2, true),
                'is_active' => true,
            ])->id,
            'pos_product_id' => 'POS-'.$sku,
            'sku' => $sku,
            'name' => fake()->words(3, true),
            'weight_grams' => 500,
            'is_active' => true,
            'synced_at' => now(),
        ];
    }
}
