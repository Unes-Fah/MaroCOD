<?php

namespace App\Policies;

use App\Models\Merchant;
use App\Models\Order;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
   
    public function update(Merchant $merchant, Order $order): Response
    {
        return $order->merchant_id === $merchant->id ? Response::allow() : Response::deny('This order does not belong to you.');
       
    }

    
}
