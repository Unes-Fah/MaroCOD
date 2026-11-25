<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Carrier;
use App\Models\Merchant;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
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
        ], $overrides));
    }

    private function makeCarrier(): Carrier
    {
        $carrier = Carrier::create(['name' => 'Amana']);
        $carrier->rates()->create(['city' => 'Casablanca', 'price' => 35]);

        return $carrier;
    }

    public function test_guest_cannot_list_orders(): void
    {
        $this->getJson('/api/orders')->assertUnauthorized();
    }

    public function test_merchant_only_sees_their_own_orders(): void
    {
        $sara = Merchant::factory()->create();
        $omar = Merchant::factory()->create();
        $this->makeOrder($sara);
        $this->makeOrder($omar);

        Sanctum::actingAs($sara);

        $this->getJson('/api/orders')
            ->assertOk()
            ->assertJsonCount(1);
    }

    public function test_pending_order_cannot_jump_to_delivered(): void
    {
        $merchant = Merchant::factory()->create();
        $order = $this->makeOrder($merchant);
        Sanctum::actingAs($merchant);

        $this->patchJson("/api/orders/{$order->id}/status", ['status' => 'delivered'])
            ->assertStatus(422);

        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
    }

    public function test_unknown_status_is_rejected_by_validation(): void
    {
        $merchant = Merchant::factory()->create();
        $order = $this->makeOrder($merchant);
        Sanctum::actingAs($merchant);

        $this->patchJson("/api/orders/{$order->id}/status", ['status' => 'delivred'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('status');
    }

    public function test_cannot_ship_without_a_carrier(): void
    {
        $merchant = Merchant::factory()->create();
        $order = $this->makeOrder($merchant, ['status' => OrderStatus::Confirmed]);
        Sanctum::actingAs($merchant);

        $this->patchJson("/api/orders/{$order->id}/status", [
            'status' => 'shipped',
            'tracking_number' => 'AM123456',
        ])->assertStatus(422);
    }

    public function test_cannot_ship_without_a_tracking_number(): void
    {
        $merchant = Merchant::factory()->create();
        $carrier = $this->makeCarrier();
        $order = $this->makeOrder($merchant, [
            'status' => OrderStatus::Confirmed,
            'carrier_id' => $carrier->id,
        ]);
        Sanctum::actingAs($merchant);

        $this->patchJson("/api/orders/{$order->id}/status", ['status' => 'shipped'])
            ->assertStatus(422);
    }

    public function test_confirmed_order_with_carrier_and_tracking_can_ship(): void
    {
        $merchant = Merchant::factory()->create();
        $carrier = $this->makeCarrier();
        $order = $this->makeOrder($merchant, [
            'status' => OrderStatus::Confirmed,
            'carrier_id' => $carrier->id,
        ]);
        Sanctum::actingAs($merchant);

        $this->patchJson("/api/orders/{$order->id}/status", [
            'status' => 'shipped',
            'tracking_number' => 'AM123456',
        ])->assertOk();

        $this->assertSame(OrderStatus::Shipped, $order->fresh()->status);
    }

    public function test_merchant_cannot_touch_another_merchants_order(): void
    {
        $sara = Merchant::factory()->create();
        $omar = Merchant::factory()->create();
        $order = $this->makeOrder($omar);
        Sanctum::actingAs($sara);

        $this->patchJson("/api/orders/{$order->id}/status", ['status' => 'confirmed'])
            ->assertForbidden();
    }

    public function test_assigning_a_carrier_snapshots_the_shipping_cost(): void
    {
        $merchant = Merchant::factory()->create();
        $carrier = $this->makeCarrier();
        $order = $this->makeOrder($merchant);
        Sanctum::actingAs($merchant);

        $this->patchJson("/api/orders/{$order->id}/carrier", ['carrier_id' => $carrier->id])
            ->assertOk();

        $this->assertEquals(35, $order->fresh()->shipping_cost);

        // The tariff changes later, and the order keeps the price it was given
        $carrier->rates()->update(['price' => 99]);
        $this->assertEquals(35, $order->fresh()->shipping_cost);
    }

    public function test_audit_command_expires_only_stale_pending_orders(): void
    {
        $merchant = Merchant::factory()->create();
        $stale     = $this->makeOrder($merchant, ['created_at' => now()->subHours(72)]);
        $fresh     = $this->makeOrder($merchant, ['created_at' => now()->subHours(10)]);
        $confirmed = $this->makeOrder($merchant, [
            'status' => OrderStatus::Confirmed,
            'created_at' => now()->subHours(72),
        ]);

        $this->artisan('orders:audit-pending')->assertSuccessful();

        $this->assertSame(OrderStatus::Expired, $stale->fresh()->status);
        $this->assertSame(OrderStatus::Pending, $fresh->fresh()->status);
        $this->assertSame(OrderStatus::Confirmed, $confirmed->fresh()->status);
    }
}