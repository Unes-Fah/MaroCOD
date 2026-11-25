<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Merchant;
use App\Models\Carrier;
use App\Models\CarrierRate;
use App\Enums\OrderStatus;
/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = fake()->randomElement(['Casablanca', 'Rabat', 'Marrakech', 'Tanger', 'Fes', 'Agadir', 'Oujda', 'Laayoune']);
        $status = fake()->randomElement(OrderStatus::cases());

        $wasShipped = in_array($status, [OrderStatus::Shipped, OrderStatus::Delivered, OrderStatus::Returned], true);
        $rate = $wasShipped ? CarrierRate::where('city', $city)->inRandomOrder()->first() : null;

        return [
            'merchant_id'     => Merchant::factory(),
            'carrier_id'      => $rate?->carrier_id,
            'shipping_cost'   => $rate?->price,
            'tracking_number' => $rate ? strtoupper(fake()->bothify('??########')) : null,
            'customer_name'   => fake()->name(),
            'customer_phone'  => '06' . fake()->numerify('########'),
            'city'            => $city,
            'total_amount'    => fake()->randomFloat(2, 80, 1500),
            'status'          => $status,
            'created_at'      => fake()->dateTimeBetween('-90 days'),
        ];
    }
}
