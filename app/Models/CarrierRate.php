<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class CarrierRate extends Model
{
    protected $fillable = ['carrier_id', 'city', 'price'];

    public function carrier():BelongsTo{
        return $this->belongsTo(Carrier::class);
    }
}
