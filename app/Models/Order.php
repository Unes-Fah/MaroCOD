<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\OrderStatus;

/**
 * @property OrderStatus $status
 */

class Order extends Model
{
    use HasFactory;
     protected function casts(): array {
        return [
            'status' => OrderStatus::class,
        ];
    }
    protected $fillable = [
        'merchant_id',
        'carrier_id',
        'customer_name',
        'customer_phone',
        'city',
        'total_amount',
        'status',
        'tracking_number',
        'shipping_cost',
    ];

   

    public function merchant():BelongsTo{
        return $this->belongsTo(Merchant::class);
    }

    public function carrier():BelongsTo{
        return $this->belongsTo(Carrier::class);
    }
}
