<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public static function legalTransitions(): array
    {
        return [
            'pending → confirmed' => [OrderStatus::Pending, OrderStatus::Confirmed],
            'pending → expired'   => [OrderStatus::Pending, OrderStatus::Expired],
            'confirmed → shipped' => [OrderStatus::Confirmed, OrderStatus::Shipped],
            'shipped → delivered' => [OrderStatus::Shipped, OrderStatus::Delivered],
            'shipped → returned'  => [OrderStatus::Shipped, OrderStatus::Returned],
        ];
    }

    public static function illegalTransitions(): array
    {
        return [
            'pending → delivered'   => [OrderStatus::Pending, OrderStatus::Delivered],
            'pending → shipped'     => [OrderStatus::Pending, OrderStatus::Shipped],
            'shipped → confirmed'   => [OrderStatus::Shipped, OrderStatus::Confirmed],
            'delivered → returned'  => [OrderStatus::Delivered, OrderStatus::Returned],
            'expired → pending'     => [OrderStatus::Expired, OrderStatus::Pending],
        ];
    }

    #[DataProvider('legalTransitions')]
    public function test_legal_transitions_are_allowed(OrderStatus $from, OrderStatus $to): void
    {
        $this->assertTrue($from->canTransitionTo($to));
    }

    #[DataProvider('illegalTransitions')]
    public function test_illegal_transitions_are_blocked(OrderStatus $from, OrderStatus $to): void
    {
        $this->assertFalse($from->canTransitionTo($to));
    }

    public function test_terminal_statuses_allow_nothing(): void
    {
        foreach ([OrderStatus::Delivered, OrderStatus::Returned, OrderStatus::Cancelled, OrderStatus::Expired] as $status) {
            $this->assertSame([], $status->allowedTransitions());
        }
    }

    public function test_every_status_is_handled_by_the_match(): void
    {
        foreach (OrderStatus::cases() as $status) {
            $this->assertIsArray($status->allowedTransitions());
        }
    }
}