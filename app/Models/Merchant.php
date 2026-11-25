<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Merchant extends Authenticatable
{
    use HasApiTokens, HasFactory;


    protected $fillable = ['name', 'store_name', 'email', 'password'];
    protected $hidden = ['password'];
    
    protected function casts():array {
        return ['password'=>'hashed'];
    }

    public function orders(): HasMany {
        return $this->hasMany(Order::class);
    }
}
