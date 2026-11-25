<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending   = 'pending';
    case Confirmed = 'confirmed';
    case Shipped   = 'shipped';
    case Delivered = 'delivered';
    case Returned  = 'returned';
    case Cancelled = 'cancelled';
    case Expired   = 'expired';

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending   => [self::Confirmed, self::Cancelled, self::Expired],
            self::Confirmed => [self::Shipped, self::Cancelled],
            self::Shipped   => [self::Delivered, self::Returned],
            self::Delivered,
            self::Returned,
            self::Cancelled,
            self::Expired   => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), true);
    }
}