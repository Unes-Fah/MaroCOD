<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Enums\OrderStatus;
use App\Models\Carrier;
use App\Models\Merchant;
use App\Models\Order;
use Laravel\Sanctum\Sanctum;

class StatisticsTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(Merchant $merchant, array $overrides = []): Order
    {
        return Order::factory()->create(array_merge([
            'merchant_id'     => $merchant->id,
            'city'            => 'Casablanca',
            'status'          => OrderStatus::Pending,
            'carrier_id'      => null,
            'shipping_cost'   => null,
            'tracking_number' => null,
            'total_amount' => 100,
        ], $overrides));
    }

    private function makeCarrier(): Carrier
    {
        $carrier = Carrier::create(['name' => 'Amana']);
        return $carrier;
    }

    public function test_financial_summary_returns_the_same_values(): void
    {
    
        $carrier = $this->makeCarrier();
        $merchant = Merchant::factory()->create();

        $this->makeOrder($merchant, [
            'status' => OrderStatus::Delivered,
            'carrier_id' => $carrier->id,
            'tracking_number' => 'A111111111',
            'total_amount' => 349.50,
            'shipping_cost' => 35,
        ]);

        $this->makeOrder($merchant, [
            'status' => OrderStatus::Returned,
            'carrier_id' => $carrier->id,
            'tracking_number' => 'A111111112',
            'total_amount' => 525,
            'shipping_cost' => 35,
        ]);

        Sanctum::actingAs($merchant);

        $response = $this->getJson('/api/stats/financial-summary')->assertOk();

        $this->assertEqualsWithDelta(279.50, $response->json('net_cash'), 0.001);
        
        $response->assertJsonPath('delivered_count', 1)
        ->assertJsonPath('returned_count', 1)
        ->assertJsonPath('success_rate', 50);

    }
    
    public function test_new_merchant_gets_zeros_and_null_success_rate(): void
    {
        $merchant = Merchant::factory()->create();
        Sanctum::actingAs($merchant);

        $this->getJson('/api/stats/financial-summary')
            ->assertOk()
            ->assertJsonPath('net_cash', 0)
            ->assertJsonPath('success_rate', null);
    }

    public function test_stats_never_include_another_merchants_orders(): void
    {
        $sara = Merchant::factory()->create();
        $omar = Merchant::factory()->create();

        $this->makeOrder($omar, ['status' => OrderStatus::Delivered, 'total_amount' => 999, 'shipping_cost' => 30]);

        Sanctum::actingAs($sara);

        $this->getJson('/api/stats/financial-summary')
            ->assertJsonPath('revenue', 0);
    }
}
