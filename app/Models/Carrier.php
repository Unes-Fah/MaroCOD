<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrier extends Model
{
    protected $fillable = ['name'];
    
    public function rates(): HasMany {
        return $this->hasMany(CarrierRate::class);
    }

    public function orders():HasMany{
        return $this->hasMany(Order::class);
    }
}
