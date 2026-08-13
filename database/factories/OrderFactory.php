<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $suffix = strtoupper(Str::random(5));

        return [
            'order_number' => 'PK-'.now()->format('Ymd').'-'.$suffix,
            'idempotency_key' => (string) Str::uuid(),
            'user_id' => User::factory()->activeCustomer(),
            'status' => 'unpaid',
            'order_date_local' => now('Asia/Jakarta')->toDateString(),
            'recipient_name' => fake()->name(),
            'recipient_phone' => fake()->numerify('08##########'),
            'shipping_address_line' => fake()->streetAddress(),
            'shipping_province' => 'Jawa Barat',
            'shipping_city' => 'Bandung',
            'shipping_district' => 'Coblong',
            'shipping_postal_code' => '40135',
            'courier_code' => 'store',
            'courier_service' => 'Kurir Toko',
            'shipping_cost' => 15000,
            'subtotal' => 100000,
            'tax_pph22' => 0,
            'grand_total' => 115000,
            'expires_at' => now()->addDay(),
        ];
    }
}
